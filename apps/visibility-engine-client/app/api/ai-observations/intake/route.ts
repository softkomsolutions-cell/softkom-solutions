import { timingSafeEqual } from "node:crypto";
import { NextResponse } from "next/server";
import { z } from "zod";
import { db } from "@/lib/db";

export const runtime = "nodejs";

const payload = z.object({
  siteId: z.string().min(1).max(128),
  platform: z.string().trim().min(1).max(120),
  prompt: z.string().trim().min(1).max(12000),
  brandMentioned: z.boolean(),
  pageCited: z.boolean(),
  citationUrl: z.string().url().max(2048).nullable().optional(),
  competitorMentioned: z.string().trim().max(2000).nullable().optional(),
  observedAt: z.coerce.date(),
  confidence: z.string().trim().min(1).max(120),
  source: z.string().trim().min(1).max(500),
});

function authorised(request: Request) {
  const configured = process.env.AI_OBSERVATION_WEBHOOK_SECRET;
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
    return NextResponse.json(
      { error: "Invalid AI observation payload." },
      { status: 400 },
    );
  }

  if (input.pageCited && !input.citationUrl)
    return NextResponse.json(
      { error: "A cited page URL is required when pageCited is true." },
      { status: 409 },
    );

  const site = await db.site.findUnique({
    where: { id: input.siteId },
    select: { id: true, baseUrl: true, organisationId: true },
  });
  if (!site)
    return NextResponse.json({ error: "Site not found." }, { status: 404 });

  if (input.citationUrl) {
    const citation = new URL(input.citationUrl);
    const base = new URL(site.baseUrl);
    if (citation.origin !== base.origin)
      return NextResponse.json(
        { error: "Citation URL does not belong to this site." },
        { status: 409 },
      );
  }

  const observation = await db.$transaction(async (tx) => {
    const saved = await tx.aIObservation.create({
      data: {
        siteId: site.id,
        platform: input.platform,
        prompt: input.prompt,
        brandMentioned: input.brandMentioned,
        pageCited: input.pageCited,
        citationUrl: input.citationUrl || null,
        competitorMentioned: input.competitorMentioned || null,
        observedAt: input.observedAt,
        confidence: input.confidence,
        source: input.source,
      },
    });

    await tx.auditEvent.create({
      data: {
        organisationId: site.organisationId,
        actorId: "ai-observation-webhook",
        action: "ai_observation.webhook",
        entityId: saved.id,
        detail: { siteId: site.id, platform: input.platform },
      },
    });
    return saved;
  });

  return NextResponse.json(
    { ok: true, id: observation.id },
    { status: 200 },
  );
}
