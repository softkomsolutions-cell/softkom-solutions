import { randomUUID } from "node:crypto";
import { NextResponse } from "next/server";
import { z } from "zod";
import { db } from "@/lib/db";
import { getContext } from "@/lib/context";
import { payfastConfig, payfastSignature, subscriptionFields } from "@/lib/payfast";
import { canManage } from "@/lib/security";

export const dynamic = "force-dynamic";

export async function POST(request: Request) {
  const ctx = await getContext();
  if (!canManage(ctx.role))
    return NextResponse.json({ error: "Workspace owner or manager required" }, { status: 403 });
  const origin = request.headers.get("origin");
  const host = request.headers.get("host");
  if (!origin || !host || new URL(origin).host !== host)
    return NextResponse.json({ error: "Invalid checkout origin" }, { status: 403 });
  const parsed = z.object({ plan: z.enum(["STARTER", "GROWTH", "PRO"]) })
    .safeParse(await request.json().catch(() => null));
  if (!parsed.success)
    return NextResponse.json({ error: "Invalid plan" }, { status: 400 });
  const config = payfastConfig();
  if (!config)
    return NextResponse.json({ error: "Payment gateway is not configured" }, { status: 503 });
  const paymentId = randomUUID();
  // Pending checkout is audit-only. It never grants a paid entitlement.
  await db.auditEvent.create({
    data: {
      organisationId: ctx.organisationId,
      actorId: ctx.user.id,
      action: "billing.checkout_requested",
      entityId: paymentId,
      detail: { plan: parsed.data.plan, provider: "PAYFAST", sandbox: config.sandbox },
    },
  });
  const fields = subscriptionFields({
    merchantId: config.merchantId, merchantKey: config.merchantKey,
    origin: config.origin, email: ctx.user.email, paymentId,
    plan: parsed.data.plan,
  });
  return NextResponse.json({
    checkoutUrl: config.processUrl,
    fields: { ...fields, signature: payfastSignature(fields, config.passphrase) },
    sandbox: config.sandbox,
  });
}
