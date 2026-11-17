import { NextResponse } from "next/server";
import { timingSafeEqual } from "node:crypto";
import { db } from "@/lib/db";
import { PLANS } from "@/lib/plans";
import { payfastConfig, payfastSignature } from "@/lib/payfast";

export const dynamic = "force-dynamic";
const respond = (status: number) => new NextResponse(null, { status });
const validHash = (a: string, b: string) => {
  const left = Buffer.from(a.toLowerCase());
  const right = Buffer.from(b.toLowerCase());
  return left.length === right.length && timingSafeEqual(left, right);
};

export async function POST(request: Request) {
  const config = payfastConfig();
  if (!config) return respond(503);
  const raw = await request.text();
  if (raw.length > 16000) return respond(413);
  const entries = new URLSearchParams(raw);
  const fields: Record<string, string> = {};
  for (const [key, value] of entries) {
    if (Object.hasOwn(fields, key)) return respond(400);
    fields[key] = value;
  }
  const signature = fields.signature;
  const paymentId = fields.m_payment_id;
  const transactionId = fields.pf_payment_id;
  if (!signature || !paymentId || !transactionId || fields.merchant_id !== config.merchantId)
    return respond(400);
  if (!validHash(signature, payfastSignature(fields, config.passphrase))) return respond(403);
  // PayFast's validation service independently confirms the received ITN body.
  const validationUrl = config.sandbox
    ? "https://sandbox.payfast.co.za/eng/query/validate"
    : "https://www.payfast.co.za/eng/query/validate";
  let validated = false;
  try {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 8000);
    try {
      const result = await fetch(validationUrl, {
        method: "POST",
        headers: { "content-type": "application/x-www-form-urlencoded" },
        body: raw,
        signal: controller.signal,
        cache: "no-store",
      });
      validated = result.ok && (await result.text()).trim() === "VALID";
    } finally { clearTimeout(timeout); }
  } catch { return respond(503); }
  if (!validated) return respond(403);
  const pending = await db.auditEvent.findFirst({
    where: { action: "billing.checkout_requested", entityId: paymentId },
    orderBy: { createdAt: "desc" },
  });
  if (!pending || Date.now() - pending.createdAt.getTime() > 24 * 60 * 60 * 1000)
    return respond(404);
  const detail = pending.detail;
  if (!detail || typeof detail !== "object" || Array.isArray(detail)) return respond(400);
  const plan = (detail as Record<string, unknown>).plan;
  if (plan !== "STARTER" && plan !== "GROWTH" && plan !== "PRO") return respond(400);
  if ((detail as Record<string, unknown>).sandbox !== config.sandbox) return respond(400);
  const expected = PLANS[plan].monthlyPrice;
  const amount = Number(fields.amount_gross);
  if (!Number.isFinite(amount) || Math.abs(amount - expected) > 0.009) return respond(400);
  if (fields.payment_status !== "COMPLETE") return respond(200);
  try {
    await db.$transaction(async (tx) => {
      // Transaction ID is globally unique at PayFast; duplicate ITNs cannot re-activate a plan.
      const existing = await tx.auditEvent.findFirst({
        where: { action: "billing.payment_confirmed", entityId: transactionId },
      });
      if (existing) return;
      const subscription = await tx.subscription.findUnique({
        where: { organisationId: pending.organisationId },
      });
      if (subscription?.providerSubscriptionId && subscription.providerSubscriptionId !== fields.token)
        throw new Error("Existing subscription token mismatch");
      const periodEnd = new Date(Date.now() + 31 * 24 * 60 * 60 * 1000);
      await tx.subscription.upsert({
        where: { organisationId: pending.organisationId },
        create: {
          organisationId: pending.organisationId, plan, status: "ACTIVE",
          monthlyPrice: expected, provider: "PAYFAST",
          providerSubscriptionId: fields.token || null, currentPeriodEndsAt: periodEnd,
        },
        update: {
          plan, status: "ACTIVE", monthlyPrice: expected, provider: "PAYFAST",
          providerSubscriptionId: fields.token || subscription?.providerSubscriptionId || null,
          currentPeriodEndsAt: periodEnd, cancelAtPeriodEnd: false,
        },
      });
      await tx.auditEvent.create({
        data: {
          organisationId: pending.organisationId,
          actorId: "billing:payfast",
          action: "billing.payment_confirmed",
          entityId: transactionId,
          detail: { paymentId, plan, amount, sandbox: config.sandbox },
        },
      });
    }, { isolationLevel: "Serializable" });
  } catch { return respond(409); }
  return respond(200);
}
