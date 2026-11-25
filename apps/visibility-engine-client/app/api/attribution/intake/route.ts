import { timingSafeEqual } from "node:crypto";
import { NextResponse } from "next/server";
import { z } from "zod";
import { db } from "@/lib/db";

export const runtime = "nodejs";

const payload = z.object({
  siteId: z.string().min(1).max(128),
  landingUrl: z.string().url().max(2048),
  source: z.string().trim().max(300).optional().default(""),
  medium: z.string().trim().max(300).optional().default(""),
  campaign: z.string().trim().max(300).optional().default(""),
  funnel: z.string().trim().min(1).max(500),
  leadId: z.string().trim().min(1).max(300),
  stage: z
    .enum(["LEAD", "QUALIFIED", "OPPORTUNITY", "WON", "LOST"])
    .optional()
    .default("LEAD"),
  revenue: z.coerce.number().min(0).max(999999999).optional().default(0),
  currency: z.string().regex(/^[A-Z]{3}$/).optional().default("ZAR"),
  createdAt: z.coerce.date().optional(),
});

function authorised(request: Request) {
  const configured = process.env.ATTRIBUTION_WEBHOOK_SECRET;
  const header = request.headers.get("authorization") || "";
  if (!configured || !header.startsWith("Bearer ")) return false;
  const supplied = header.slice(7);
  const a = Buffer.from(configured);
  const b = Buffer.from(supplied);
  return a.length === b.length && timingSafeEqual(a, b);
}

export async function POST(request: Request) {
  if (!authorised(request))
    return NextResponse.json({ error: "Unauthorised." }, { status: 401 });

  let input: z.infer<typeof payload>;
  try {
    input = payload.parse(await request.json());
  } catch {
    return NextResponse.json({ error: "Invalid attribution payload." }, { status: 400 });
  }

  if (input.revenue > 0 && input.stage !== "WON")
    return NextResponse.json(
      { error: "Revenue can only be recorded for a won lead." },
      { status: 409 },
    );

  const site = await db.site.findUnique({
    where: { id: input.siteId },
    select: { id: true, baseUrl: true, organisationId: true },
  });
  if (!site)
    return NextResponse.json({ error: "Site not found." }, { status: 404 });

  const landing = new URL(input.landingUrl);
  const base = new URL(site.baseUrl);
  if (landing.origin !== base.origin)
    return NextResponse.json(
      { error: "Landing page does not belong to this site." },
      { status: 409 },
    );

  const publication = await db.publication.findFirst({
    where: {
      siteId: site.id,
      status: "LIVE",
      liveUrl: input.landingUrl,
    },
    select: {
      contentId: true,
      content: { select: { topic: { select: { cluster: true } } } },
    },
  });

  const lead = await db.$transaction(async (tx) => {
    const saved = await tx.leadAttribution.upsert({
      where: {
        siteId_leadId: {
          siteId: site.id,
          leadId: input.leadId,
        },
      },
      create: {
        siteId: site.id,
        landingUrl: input.landingUrl,
        source: input.source || null,
        medium: input.medium || null,
        campaign: input.campaign || null,
        funnel: input.funnel,
        leadId: input.leadId,
        stage: input.stage,
        revenue: input.revenue || null,
        currency: input.currency,
        createdAt: input.createdAt || new Date(),
        dataOrigin: "WEBHOOK",
        contentId: publication?.contentId || null,
        cluster: publication?.content?.topic?.cluster || null,
      },
      update: {
        landingUrl: input.landingUrl,
        source: input.source || null,
        medium: input.medium || null,
        campaign: input.campaign || null,
        funnel: input.funnel,
        stage: input.stage,
        revenue: input.revenue || null,
        currency: input.currency,
        dataOrigin: "WEBHOOK",
        contentId: publication?.contentId || null,
        cluster: publication?.content?.topic?.cluster || null,
      },
    });

    await tx.auditEvent.create({
      data: {
        organisationId: site.organisationId,
        actorId: "attribution-webhook",
        action: "attribution.webhook",
        entityId: saved.id,
        detail: { siteId: site.id, leadId: input.leadId },
      },
    });
    return saved;
  });

  return NextResponse.json(
    { ok: true, id: lead.id, stage: lead.stage },
    { status: 200 },
  );
}
