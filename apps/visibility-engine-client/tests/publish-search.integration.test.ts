import { beforeAll, afterAll, describe, it, expect } from "vitest";
import { randomUUID } from "node:crypto";
import { db } from "../lib/db";
import type { Context } from "../lib/context";
import {
  addSite,
  createTopic,
  createContent,
  reviewContent,
  reviseContent,
  requestPublication,
} from "../lib/workflow";
import {
  connectWordPress,
  disconnect,
  connectionSelect,
} from "../lib/connections/service";
import { decryptCredential } from "../lib/connections/credentials";
import { processPublication, retryPublication } from "../lib/publishing/worker";
import { wordpressFixture } from "./fixtures/providers";
import {
  beginGoogle,
  finishGoogle,
  chooseProperty,
  googleProperties,
  disconnectGoogle,
} from "../lib/analytics/service";
import { processSync, requestSync, syncWindow } from "../lib/analytics/sync";
import { type Transport } from "../lib/connections/http";
import { workspaceData } from "../lib/queries";
let ctx: Context, foreign: Context, siteId: string;
const orgs: string[] = [],
  users: string[] = [];
const fixture = wordpressFixture();
async function organisation(name: string): Promise<Context> {
  const organisation = await db.organisation.create({
    data: { name, slug: randomUUID() },
  });
  orgs.push(organisation.id);
  const user = await db.user.create({
    data: { email: `${randomUUID()}@example.test`, name },
  });
  users.push(user.id);
  await db.membership.create({
    data: { organisationId: organisation.id, userId: user.id, role: "OWNER" },
  });
  return {
    organisation,
    organisationId: organisation.id,
    user: { ...user, name, emailVerified: false },
    role: "OWNER",
    memberships: [],
  } as Context;
}
async function content(reviewerId = "") {
  const topic = await createTopic(ctx, {
    siteId,
    title: "Revenue opportunity",
    slug: `topic-${randomUUID()}`,
    cluster: "Revenue",
    intent: "COMMERCIAL",
    priority: 80,
    funnel: "/contact",
    reasoning: "A documented business need",
    businessProblem: "Missed enquiries",
    contentType: "Commercial page",
    evidence: "Test brief",
  });
  return createContent(ctx, {
    siteId,
    topicId: topic.id,
    title: "Approved original title",
    slug: `content-${randomUUID()}`,
    body: "<p>Exact approved original revision</p>",
    metaTitle: "Approved metadata",
    metaDesc: "Approved description",
    funnel: "/contact",
    contentType: "Page",
    reviewerId,
    internalLinks: "",
    scheduledAt: "",
  });
}
const googleCalls: string[] = [];
let rateLimit = false;
const google: Transport = async (value, options = {}) => {
  googleCalls.push(value);
  const url = new URL(value);
  let body: unknown = {};
  let status = 200;
  if (url.pathname === "/token")
    body = {
      access_token: "fixture-access-token",
      refresh_token: "fixture-refresh-token",
      expires_in: 3600,
    };
  else if (url.pathname === "/revoke") body = {};
  else if (url.pathname.endsWith("/sites"))
    body = {
      siteEntry: [
        {
          siteUrl: "sc-domain:wordpress.example",
          permissionLevel: "siteOwner",
        },
        {
          siteUrl: "https://wordpress.example/",
          permissionLevel: "siteFullUser",
        },
      ],
    };
  else if (url.pathname.endsWith("/searchAnalytics/query")) {
    if (rateLimit) {
      status = 429;
      body = { error: "fixture secret must not escape" };
    } else {
      const input = JSON.parse(options.body!);
      body = {
        rows: [
          {
            keys: [
              input.endDate,
              "business automation",
              "https://wordpress.example/measured/",
            ],
            clicks: 9,
            impressions: 120,
            ctr: 0.075,
            position: 4.5,
          },
        ],
      };
    }
  }
  return { status, body: JSON.stringify(body), headers: {}, url: value };
};
beforeAll(async () => {
  process.env.GOOGLE_CLIENT_ID = "fixture-client";
  process.env.GOOGLE_CLIENT_SECRET = "fixture-client-secret";
  process.env.GOOGLE_CALLBACK_URL =
    "http://localhost:3000/api/connections/google/callback";
  ctx = await organisation("Phase2 tenant");
  foreign = await organisation("Foreign phase2 tenant");
  siteId = (
    await addSite(ctx, {
      name: "WordPress fixture",
      baseUrl: "https://wordpress.example",
      cms: "WORDPRESS",
    })
  ).id;
});
afterAll(async () => {
  await db.oAuthState.deleteMany({ where: { organisationId: { in: orgs } } });
  await db.contentComment.deleteMany({ where: { authorId: { in: users } } });
  await db.contentItem.deleteMany({
    where: { site: { organisationId: { in: orgs } } },
  });
  await db.organisation.deleteMany({ where: { id: { in: orgs } } });
  await db.user.deleteMany({ where: { id: { in: users } } });
  await db.$disconnect();
});
describe("WordPress connections and publication worker", () => {
  it("encrypts per-site credentials and never serialises them to workspace data", async () => {
    await expect(
      connectWordPress(
        foreign,
        {
          siteId,
          siteUrl: "https://wordpress.example",
          username: "fixture",
          password: "private-test-password",
          label: "Fixture",
        },
        fixture.transport,
      ),
    ).rejects.toMatchObject({ status: 404 });
    await connectWordPress(
      ctx,
      {
        siteId,
        siteUrl: "https://wordpress.example",
        username: "fixture",
        password: "private-test-password",
        label: "Fixture",
      },
      fixture.transport,
    );
    const c = await db.siteConnection.findUniqueOrThrow({
      where: { siteId_provider: { siteId, provider: "WORDPRESS" } },
    });
    expect(c.status).toBe("CONNECTED");
    expect(c.encryptedCredentials).not.toContain("private-test-password");
    expect(decryptCredential(c.encryptedCredentials!)).toContain(
      "private-test-password",
    );
    expect(JSON.stringify(await workspaceData(ctx))).not.toContain(
      "private-test-password",
    );
    expect(JSON.stringify(await workspaceData(ctx))).not.toContain(
      c.encryptedCredentials!,
    );
    expect(
      JSON.stringify(
        await db.siteConnection.findMany({ select: connectionSelect }),
      ),
    ).not.toContain("private-test-password");
  });
  it("publishes the requested snapshot after a newer editable revision exists", async () => {
    const c = await content();
    await reviewContent(ctx, {
      id: c.id,
      revision: 1,
      decision: "APPROVED",
      comment: "Approved",
    });
    const p = await requestPublication(ctx, { id: c.id });
    expect(p.status).toBe("PENDING");
    expect((await requestPublication(ctx, { id: c.id })).id).toBe(p.id);
    await reviseContent(ctx, {
      id: c.id,
      revision: 1,
      title: "Unapproved newer title",
      body: "<p>Unapproved changed body</p>",
      metaTitle: "New",
      metaDesc: "New",
      internalLinks: "",
    });
    const results = await Promise.all([
      processPublication(p.id, fixture.transport),
      processPublication(p.id, fixture.transport),
    ]);
    expect(results.filter(Boolean)).toHaveLength(1);
    const published = await db.publication.findUniqueOrThrow({
      where: { id: p.id },
    });
    expect(published.status).toBe("LIVE");
    expect(published.verifiedAt).not.toBeNull();
    expect(published.requestedRevision).toBe(1);
    expect(
      fixture.pages.get(Number(published.externalId))?.content.raw,
    ).toContain("Exact approved original revision");
    expect(
      fixture.pages.get(Number(published.externalId))?.content.raw,
    ).not.toContain("Unapproved");
    expect(
      (await db.contentItem.findUniqueOrThrow({ where: { id: c.id } })).status,
    ).toBe("CLIENT_REVIEW");
    expect(await processPublication(p.id, fixture.transport)).toBe(false);
  });
  it("retains remote identity on failed verification and retries without duplicates", async () => {
    const c = await content();
    await reviewContent(ctx, {
      id: c.id,
      revision: 1,
      decision: "APPROVED",
      comment: "",
    });
    const p = await requestPublication(ctx, { id: c.id });
    const before = fixture.creates;
    fixture.failVerification = true;
    await processPublication(p.id, fixture.transport);
    let failed = await db.publication.findUniqueOrThrow({
      where: { id: p.id },
    });
    expect(failed.status).toBe("FAILED");
    expect(failed.externalId).not.toBeNull();
    expect(failed.verificationError).toContain("verified");
    expect(failed.nextRetryAt).not.toBeNull();
    await expect(retryPublication(foreign, p.id)).rejects.toMatchObject({
      status: 404,
    });
    fixture.failVerification = false;
    await retryPublication(ctx, p.id);
    await processPublication(p.id, fixture.transport);
    failed = await db.publication.findUniqueOrThrow({ where: { id: p.id } });
    expect(failed.status).toBe("LIVE");
    expect(failed.attempts).toBe(2);
    expect(fixture.creates - before).toBe(1);
  });
  it("recovers a lost API response by deterministic remote identity", async () => {
    const c = await content();
    await reviewContent(ctx, {
      id: c.id,
      revision: 1,
      decision: "APPROVED",
      comment: "",
    });
    const p = await requestPublication(ctx, { id: c.id });
    const before = fixture.creates;
    fixture.loseResponse = true;
    await processPublication(p.id, fixture.transport);
    await retryPublication(ctx, p.id);
    await processPublication(p.id, fixture.transport);
    expect(fixture.creates - before).toBe(1);
    expect(
      (await db.publication.findUniqueOrThrow({ where: { id: p.id } })).status,
    ).toBe("LIVE");
  });
  it("enforces assigned reviewer and same-organisation reviewer rights", async () => {
    const c = await content(ctx.user.id);
    await expect(
      reviewContent(
        { ...ctx, user: foreign.user },
        { id: c.id, revision: 1, decision: "APPROVED", comment: "" },
      ),
    ).rejects.toMatchObject({ status: 403 });
    await expect(
      reviewContent(
        { ...ctx, role: "SOFTKOM_MANAGER" },
        { id: c.id, revision: 1, decision: "APPROVED", comment: "" },
      ),
    ).rejects.toMatchObject({ status: 403 });
    await expect(content(foreign.user.id)).rejects.toMatchObject({
      status: 404,
    });
    await reviewContent(ctx, {
      id: c.id,
      revision: 1,
      decision: "APPROVED",
      comment: "Assigned reviewer approved",
    });
  });
  it("stops retries at the maximum and sanitises stored provider failures", async () => {
    const c = await content();
    await reviewContent(ctx, {
      id: c.id,
      revision: 1,
      decision: "APPROVED",
      comment: "",
    });
    const p = await requestPublication(ctx, { id: c.id });
    await db.publication.update({ where: { id: p.id }, data: { attempts: 4 } });
    await processPublication(p.id, async () => {
      throw new Error("private-test-password");
    });
    const failed = await db.publication.findUniqueOrThrow({
      where: { id: p.id },
    });
    expect(failed.attempts).toBe(5);
    expect(failed.nextRetryAt).toBeNull();
    expect(failed.error).not.toContain("private-test-password");
    await expect(retryPublication(ctx, p.id)).rejects.toMatchObject({
      status: 409,
    });
    await db.publication.update({
      where: { id: p.id },
      data: {
        status: "PUBLISHING",
        claimToken: "expired-final-worker",
        leaseUntil: new Date(Date.now() - 1000),
      },
    });
    expect(await processPublication(p.id, fixture.transport)).toBe(false);
    expect(
      (await db.publication.findUniqueOrThrow({ where: { id: p.id } })).status,
    ).toBe("FAILED");
  });
});
describe("Google OAuth, property selection and measured ingestion", () => {
  it("binds one-time state to the user, tenant, site and generation", async () => {
    const url = new URL(await beginGoogle(ctx, siteId));
    const state = url.searchParams.get("state")!;
    expect(url.searchParams.get("code_challenge_method")).toBe("S256");
    await expect(
      finishGoogle(foreign, state, "fixture-code", google),
    ).rejects.toMatchObject({ status: 403 });
    await finishGoogle(ctx, state, "fixture-code", google);
    await expect(
      finishGoogle(ctx, state, "fixture-code", google),
    ).rejects.toMatchObject({ status: 403 });
    const c = await db.siteConnection.findUniqueOrThrow({
      where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
    });
    expect(c.status).toBe("REQUIRES_SETUP");
    expect(c.encryptedCredentials).not.toContain("fixture-refresh-token");
    expect(c.selectedProperty).toBeNull();
    await expect(
      googleProperties(foreign, siteId, google),
    ).rejects.toMatchObject({ status: 404 });
    await expect(
      chooseProperty(ctx, siteId, "sc-domain:foreign.example", google),
    ).rejects.toMatchObject({ status: 403 });
    await chooseProperty(ctx, siteId, "sc-domain:wordpress.example", google);
  });
  it("claims one sync, refreshes tokens and upserts the same measurement grain", async () => {
    const c = await db.siteConnection.findUniqueOrThrow({
      where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
    });
    const { encryptCredential } =
      await import("../lib/connections/credentials");
    await db.siteConnection.update({
      where: { id: c.id },
      data: {
        encryptedCredentials: encryptCredential(
          JSON.stringify({
            access_token: "expired",
            refresh_token: "fixture-refresh-token",
            expires_at: 0,
          }),
        ),
      },
    });
    const job = await requestSync(ctx, siteId);
    expect((await requestSync(ctx, siteId)).id).toBe(job.id);
    await expect(requestSync(foreign, siteId)).rejects.toMatchObject({
      status: 404,
    });
    const claimed = await Promise.all([
      processSync(job.id, google),
      processSync(job.id, google),
    ]);
    expect(claimed.filter(Boolean)).toHaveLength(1);
    expect(googleCalls.some((url) => url.endsWith("/token"))).toBe(true);
    let rows = await db.searchMetric.findMany({ where: { siteId } });
    expect(rows).toHaveLength(1);
    expect(rows[0].clicks).toBe(9);
    expect(rows[0].impressions).toBe(120);
    expect(rows[0].ctr).toBe(0.075);
    expect(rows[0].position).toBe(4.5);
    await requestSync(ctx, siteId);
    await processSync(job.id, google);
    rows = await db.searchMetric.findMany({ where: { siteId } });
    expect(rows).toHaveLength(1);
    const done = await db.searchSync.findUniqueOrThrow({
      where: { id: job.id },
    });
    expect(done.lastSuccessAt).not.toBeNull();
    expect(done.latestDataDate).toEqual(syncWindow().to);
    expect((await workspaceData(foreign)).metrics).toHaveLength(0);
    expect(JSON.stringify(await workspaceData(ctx))).not.toContain(
      "fixture-refresh-token",
    );
  });
  it("preserves and separates observations when the selected property changes", async () => {
    await chooseProperty(ctx, siteId, "https://wordpress.example/", google);
    expect(
      (
        await db.siteConnection.findUniqueOrThrow({
          where: {
            siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" },
          },
        })
      ).lastSyncedAt,
    ).toBeNull();
    expect((await workspaceData(ctx)).metrics).toHaveLength(0);
    const job = await requestSync(ctx, siteId);
    await processSync(job.id, google);
    expect(await db.searchMetric.count({ where: { siteId } })).toBe(2);
    const visible = (await workspaceData(ctx)).metrics;
    expect(visible).toHaveLength(1);
    expect(visible[0].property).toBe("https://wordpress.example/");
  });
  it("records rate limiting with bounded retries and removes credentials on disconnect", async () => {
    const job = await requestSync(ctx, siteId);
    rateLimit = true;
    await processSync(job.id, google);
    rateLimit = false;
    const failed = await db.searchSync.findUniqueOrThrow({
      where: { id: job.id },
    });
    expect(failed.status).toBe("FAILED");
    expect(failed.nextRetryAt).not.toBeNull();
    expect(failed.error).not.toContain("fixture secret");
    await disconnectGoogle(ctx, siteId, google);
    const c = await db.siteConnection.findUniqueOrThrow({
      where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
    });
    expect(c.status).toBe("NOT_CONNECTED");
    expect(c.encryptedCredentials).toBeNull();
    expect(await db.searchSync.count({ where: { connectionId: c.id } })).toBe(
      0,
    );
    await disconnect(ctx, siteId, "WORDPRESS");
    await expect(
      processPublication("missing", fixture.transport),
    ).resolves.toBe(false);
    const audits = JSON.stringify(
      await db.auditEvent.findMany({
        where: { organisationId: ctx.organisationId },
      }),
    );
    expect(audits).not.toContain("fixture-refresh-token");
    expect(audits).not.toContain("private-test-password");
  });
});
