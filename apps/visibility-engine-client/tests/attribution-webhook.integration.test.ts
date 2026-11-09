import { afterAll, beforeAll, describe, expect, it } from "vitest";
import { randomUUID } from "node:crypto";
import { db } from "../lib/db";
import { POST } from "../app/api/attribution/intake/route";

let organisationId = "";
let siteId = "";
const secret = "integration-attribution-secret-0123456789";

function request(body: Record<string, unknown>, authorised = true) {
  return new Request("http://localhost/api/attribution/intake", {
    method: "POST",
    headers: {
      "content-type": "application/json",
      ...(authorised ? { authorization: `Bearer ${secret}` } : {}),
    },
    body: JSON.stringify(body),
  });
}

function basePayload() {
  return {
    siteId,
    landingUrl: "https://attribution.example.test/assessment/",
    source: "chatgpt",
    medium: "ai-search",
    campaign: "integration",
    funnel: "Business Systems & AI Readiness Assessment",
    leadId: "integration-lead-1",
    stage: "LEAD",
    revenue: 0,
    currency: "ZAR",
    createdAt: "2026-11-09T09:00:00.000Z",
  };
}

beforeAll(async () => {
  process.env.ATTRIBUTION_WEBHOOK_SECRET = secret;
  const organisation = await db.organisation.create({
    data: {
      name: "Attribution webhook integration",
      slug: `attribution-${randomUUID()}`,
    },
  });
  organisationId = organisation.id;
  const site = await db.site.create({
    data: {
      organisationId,
      name: "Attribution integration site",
      baseUrl: "https://attribution.example.test",
      status: "CONNECTED",
    },
  });
  siteId = site.id;
});

afterAll(async () => {
  if (organisationId) {
    await db.organisation.delete({ where: { id: organisationId } });
  }
  delete process.env.ATTRIBUTION_WEBHOOK_SECRET;
  await db.$disconnect();
});

describe("attribution webhook intake", () => {
  it("requires the configured bearer secret", async () => {
    const response = await POST(request(basePayload(), false));
    expect(response.status).toBe(401);
    expect(
      await db.leadAttribution.count({ where: { siteId } }),
    ).toBe(0);
  });

  it("rejects landing URLs outside the connected site origin", async () => {
    const response = await POST(
      request({
        ...basePayload(),
        landingUrl: "https://foreign.example.test/assessment/",
      }),
    );
    expect(response.status).toBe(409);
    expect(
      await db.leadAttribution.count({ where: { siteId } }),
    ).toBe(0);
  });

  it("rejects realised revenue unless the lead is WON", async () => {
    const response = await POST(
      request({
        ...basePayload(),
        leadId: "integration-revenue-rule",
        stage: "QUALIFIED",
        revenue: 5000,
      }),
    );
    expect(response.status).toBe(409);
    expect(
      await db.leadAttribution.count({
        where: { siteId, leadId: "integration-revenue-rule" },
      }),
    ).toBe(0);
  });

  it("idempotently upserts the same lead and preserves webhook origin", async () => {
    const first = await POST(request(basePayload()));
    expect(first.status).toBe(200);

    const second = await POST(
      request({
        ...basePayload(),
        source: "google",
        medium: "organic",
        stage: "QUALIFIED",
      }),
    );
    expect(second.status).toBe(200);

    const rows = await db.leadAttribution.findMany({
      where: { siteId, leadId: "integration-lead-1" },
    });
    expect(rows).toHaveLength(1);
    expect(rows[0].stage).toBe("QUALIFIED");
    expect(rows[0].source).toBe("google");
    expect(rows[0].medium).toBe("organic");
    expect(rows[0].dataOrigin).toBe("WEBHOOK");
    expect(rows[0].revenue).toBeNull();
    expect(
      await db.auditEvent.count({
        where: {
          organisationId,
          action: "attribution.webhook",
          entityId: rows[0].id,
        },
      }),
    ).toBe(2);
  });
});
