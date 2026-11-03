import { afterAll, beforeAll, describe, expect, it } from "vitest";
import { randomUUID } from "node:crypto";
import { db } from "../lib/db";
import { scopedContent, scopedSite, type Context } from "../lib/context";
import { workspaceData } from "../lib/queries";
import {
  addSite,
  createContent,
  createTopic,
  recordLead,
  requestPublication,
  reviewContent,
  reviseContent,
  setTopicStatus,
} from "../lib/workflow";
let a: Context, b: Context;
let siteA: string,
  siteB: string,
  topicA: string,
  topicB: string,
  contentA: string,
  contentB: string;
const orgIds: string[] = [];
const userIds: string[] = [];
beforeAll(async () => {
  async function fixture(name: string) {
    const organisation = await db.organisation.create({
      data: { name, slug: `test-${randomUUID()}` },
    });
    orgIds.push(organisation.id);
    const user = await db.user.create({
      data: {
        name,
        email: `${randomUUID()}@example.test`,
        emailVerified: true,
      },
    });
    userIds.push(user.id);
    await db.membership.create({
      data: { organisationId: organisation.id, userId: user.id, role: "OWNER" },
    });
    return {
      organisation,
      organisationId: organisation.id,
      user: { ...user, name: name, image: null },
      role: "OWNER",
      memberships: [],
    } as Context;
  }
  a = await fixture("Tenant A integration");
  b = await fixture("Tenant B integration");
  siteA = (
    await addSite(a, {
      name: "A website",
      baseUrl: "https://a.example.test",
      cms: "WORDPRESS",
    })
  ).id;
  siteB = (
    await addSite(b, {
      name: "B website",
      baseUrl: "https://b.example.test",
      cms: "WORDPRESS",
    })
  ).id;
  const topic = (siteId: string) => ({
    siteId,
    title: "Recover lost leads",
    slug: "recover-leads",
    cluster: "Revenue Recovery",
    intent: "COMMERCIAL",
    priority: 80,
    funnel: "lost-lead-recovery",
    reasoning: "Existing offer addresses a documented business problem.",
    businessProblem: "Missed follow-up",
    contentType: "Commercial problem page",
    evidence: "Client interview",
  });
  topicA = (await createTopic(a, topic(siteA))).id;
  topicB = (await createTopic(b, topic(siteB))).id;
  const content = (siteId: string, topicId: string) => ({
    siteId,
    topicId,
    title: "A useful proposal",
    slug: "useful-proposal",
    body: "<p>Useful content</p>",
    metaTitle: "Useful proposal",
    metaDesc: "Business problems and practical next steps",
    funnel: "lost-lead-recovery",
    contentType: "Problem page",
    internalLinks: "",
    reviewerId: "",
    scheduledAt: "",
  });
  contentA = (await createContent(a, content(siteA, topicA))).id;
  contentB = (await createContent(b, content(siteB, topicB))).id;
});
afterAll(async () => {
  // Remove dependent records in a safe order; never reset an existing database.
  await db.contentComment.deleteMany({
    where: { content: { site: { organisationId: { in: orgIds } } } },
  });
  await db.leadAttribution.deleteMany({
    where: { site: { organisationId: { in: orgIds } } },
  });
  await db.contentItem.deleteMany({
    where: { site: { organisationId: { in: orgIds } } },
  });
  await db.organisation.deleteMany({ where: { id: { in: orgIds } } });
  await db.user.deleteMany({ where: { id: { in: userIds } } });
  await db.$disconnect();
});
describe("real PostgreSQL tenant isolation", () => {
  it("scopes record reads, lists and site filters by organisation", async () => {
    await expect(scopedSite(a, siteB)).rejects.toMatchObject({ status: 404 });
    await expect(scopedContent(a, contentB)).rejects.toMatchObject({
      status: 404,
    });
    await expect(workspaceData(a, { site: siteB })).rejects.toMatchObject({
      status: 404,
    });
    const list = await workspaceData(a);
    expect(list.sites.map((s) => s.id)).toEqual([siteA]);
    expect(list.content.map((c) => c.id)).toEqual([contentA]);
    expect(
      list.members.every((m) => m.organisationId === a.organisationId),
    ).toBe(true);
    expect(JSON.stringify(list)).not.toContain(contentB);
    expect(JSON.stringify(list)).not.toContain(b.user.email);
  });
  it("blocks cross-tenant reviews, revisions, statuses and publish requests", async () => {
    await expect(
      reviewContent(a, {
        id: contentB,
        revision: 1,
        decision: "APPROVED",
        comment: "",
      }),
    ).rejects.toMatchObject({ status: 404 });
    await expect(
      reviseContent(a, {
        id: contentB,
        revision: 1,
        title: "Hijacked",
        body: "<p>x</p>",
        metaTitle: "x",
        metaDesc: "x",
        internalLinks: "",
      }),
    ).rejects.toMatchObject({ status: 409 });
    await expect(
      setTopicStatus(a, { id: topicB, status: "PAUSED" }),
    ).rejects.toMatchObject({ status: 404 });
    await expect(requestPublication(a, { id: contentB })).rejects.toMatchObject(
      { status: 404 },
    );
    expect((await scopedContent(b, contentB)).title).toBe("A useful proposal");
  });
  it("blocks cross-site topic and reviewer payloads", async () => {
    const raw = {
      siteId: siteA,
      topicId: topicB,
      title: "Attempt",
      slug: "attempt",
      body: "copy",
      metaTitle: "title",
      metaDesc: "description",
      contentType: "Page",
      funnel: "review",
      reviewerId: "",
      internalLinks: "",
      scheduledAt: "",
    };
    await expect(createContent(a, raw)).rejects.toMatchObject({ status: 404 });
    await expect(
      createContent(a, { ...raw, topicId: topicA, reviewerId: b.user.id }),
    ).rejects.toMatchObject({ status: 404 });
  });
  it("enforces database-level matching of topic and content site", async () => {
    await expect(
      db.contentItem.create({
        data: {
          siteId: siteA,
          topicId: topicB,
          title: "Invalid relation",
          slug: "invalid",
          body: "invalid",
        },
      }),
    ).rejects.toThrow();
  });
  it("allows clients to review but prevents management and attribution changes", async () => {
    const client = { ...a, role: "CLIENT" as const };
    await expect(
      addSite(client, {
        name: "x",
        baseUrl: "https://x.test",
        cms: "WORDPRESS",
      }),
    ).rejects.toMatchObject({ status: 403 });
    await expect(createTopic(client, {})).rejects.toMatchObject({
      status: 403,
    });
    await expect(createContent(client, {})).rejects.toMatchObject({
      status: 403,
    });
    await expect(reviseContent(client, {})).rejects.toMatchObject({
      status: 403,
    });
    await expect(recordLead(client, {})).rejects.toMatchObject({ status: 403 });
    await expect(
      requestPublication(client, { id: contentA }),
    ).rejects.toMatchObject({ status: 403 });
    await reviewContent(client, {
      id: contentA,
      revision: 1,
      decision: "COMMENT",
      comment: "Please check the tone.",
    });
    expect((await scopedContent(a, contentA)).comments).toHaveLength(1);
  });
  it("records approvals and audit, invalidates approval when a new revision is saved", async () => {
    await expect(requestPublication(a, { id: contentA })).rejects.toMatchObject(
      { status: 409 },
    );
    await reviewContent(
      { ...a, role: "CLIENT" },
      { id: contentA, revision: 1, decision: "APPROVED", comment: "Ready." },
    );
    const failed = await requestPublication(a, { id: contentA });
    expect(failed.status).toBe("FAILED");
    expect(failed.liveUrl).toBeNull();
    expect(failed.error).toContain("not implemented");
    await reviseContent(a, {
      id: contentA,
      revision: 1,
      title: "Revised proposal",
      body: "<p>Revised safe content</p><script>bad()</script>",
      metaTitle: "Revised",
      metaDesc: "Revised description",
      internalLinks: "Link to review",
    });
    const revised = await scopedContent(a, contentA);
    expect(revised.revision).toBe(2);
    expect(revised.status).toBe("CLIENT_REVIEW");
    expect(revised.approvals[0].revision).toBe(1);
    expect(revised.revisions).toHaveLength(2);
    expect(revised.body).not.toContain("script");
    await expect(requestPublication(a, { id: contentA })).rejects.toMatchObject(
      { status: 409 },
    );
    await expect(
      reviewContent(a, {
        id: contentA,
        revision: 1,
        decision: "APPROVED",
        comment: "",
      }),
    ).rejects.toMatchObject({ status: 409 });
    expect(
      await db.auditEvent.count({
        where: {
          organisationId: a.organisationId,
          action: "content.revised",
          entityId: contentA,
        },
      }),
    ).toBe(1);
  });
  it("requires a Softkom manager override and logs the reason", async () => {
    const reason =
      "Explicit test override for a documented operational exception.";
    await expect(
      requestPublication(a, { id: contentA, overrideReason: reason }),
    ).rejects.toMatchObject({ status: 403 });
    const p = await requestPublication(
      { ...a, role: "SOFTKOM_MANAGER" },
      { id: contentA, overrideReason: reason },
    );
    expect(p.overrideReason).toBe(reason);
    expect(
      await db.auditEvent.count({
        where: {
          organisationId: a.organisationId,
          entityId: p.id,
          action: "publication.override",
        },
      }),
    ).toBe(1);
  });
  it("rejects cross-tenant attribution links and keeps valid manual results identified", async () => {
    const raw = {
      siteId: siteA,
      contentId: contentB,
      landingUrl: "https://a.example.test/page",
      source: "google",
      medium: "organic",
      campaign: "test",
      funnel: "business-review",
      leadId: "test-lead",
      stage: "WON",
      revenue: "1250.50",
      currency: "ZAR",
      date: "2026-11-03",
    };
    await expect(recordLead(a, raw)).rejects.toMatchObject({ status: 404 });
    const lead = await recordLead(a, { ...raw, contentId: contentA });
    expect(lead.dataOrigin).toBe("MANUAL");
    expect(lead.revenue?.toString()).toBe("1250.5");
    await expect(
      recordLead(a, { ...raw, contentId: contentA, stage: "QUALIFIED" }),
    ).rejects.toMatchObject({ status: 409 });
  });
});
