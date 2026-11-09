import { timingSafeEqual } from "node:crypto";
import { NextResponse } from "next/server";
import { z } from "zod";
import { db } from "@/lib/db";
import { PLANS } from "@/lib/plans";

export const dynamic = "force-dynamic";

function authorized(request: Request) {
  const secret = process.env.BILLING_WEBHOOK_SECRET || "";
  const header = request.headers.get("authorization") || "";
  const token = header.startsWith("Bearer ") ? header.slice(7) : "";
  if (!secret || secret.length < 24 || token.length !== secret.length) return false;
  return timingSafeEqual(Buffer.from(token), Buffer.from(secret));
}

const payloadSchema = z.object({
  organisationId: z.string().min(1).max(128),
  plan: z.enum(["STARTER", "GROWTH", "PRO"]),
  status: z.enum(["ACTIVE", "PAST_DUE", "CANCELLED"]),
  currency: z.enum(["ZAR", "USD"]).default("ZAR"),
  provider: z.string().min(1).max(80),
  providerCustomerId: z.string().max(300).optional(),
  providerSubscriptionId: z.string().max(300).optional(),
  currentPeriodEndsAt: z.coerce.date().optional(),
  cancelAtPeriodEnd: z.boolean().optional(),
});

export async function POST(request: Request) {
  if (!authorized(request)) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const parsed = payloadSchema.safeParse(await request.json());
  if (!parsed.success)
    return NextResponse.json({ error: "Invalid subscription payload" }, { status: 400 });

  const input = parsed.data;
  const organisation = await db.organisation.findUnique({ where: { id: input.organisationId } });
  if (!organisation) return NextResponse.json({ error: "Organisation not found" }, { status: 404 });

  const plan = PLANS[input.plan];
  const monthlyPrice = input.currency === "USD" ? plan.usdMonthlyPrice : plan.monthlyPrice;
  await db.subscription.upsert({
    where: { organisationId: input.organisationId },
    create: {
      organisationId: input.organisationId,
      plan: input.plan,
      status: input.status,
      monthlyPrice,
      currency: input.currency,
      provider: input.provider,
      providerCustomerId: input.providerCustomerId,
      providerSubscriptionId: input.providerSubscriptionId,
      currentPeriodEndsAt: input.currentPeriodEndsAt,
      cancelAtPeriodEnd: input.cancelAtPeriodEnd ?? false,
    },
    update: {
      plan: input.plan,
      status: input.status,
      monthlyPrice,
      currency: input.currency,
      provider: input.provider,
      providerCustomerId: input.providerCustomerId,
      providerSubscriptionId: input.providerSubscriptionId,
      currentPeriodEndsAt: input.currentPeriodEndsAt,
      cancelAtPeriodEnd: input.cancelAtPeriodEnd ?? false,
    },
  });
  await db.auditEvent.create({
    data: {
      organisationId: input.organisationId,
      actorId: `billing:${input.provider}`,
      action: "subscription.updated",
      entityId: input.organisationId,
      detail: {
        plan: input.plan,
        status: input.status,
        provider: input.provider,
        currency: input.currency,
        monthlyPrice,
      },
    },
  });
  return NextResponse.json({ ok: true });
}
