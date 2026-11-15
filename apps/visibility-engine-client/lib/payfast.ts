import { createHash } from "node:crypto";
import type { PlanKey } from "./plans";
import { PLANS } from "./plans";

// PayFast requires form field insertion order to be preserved when signing.
export function payfastSignature(fields: Record<string, string>, passphrase: string) {
  const encode = (value: string) => encodeURIComponent(value.trim()).replace(/%20/g, "+");
  const parts = Object.entries(fields)
    .filter(([key, value]) => key !== "signature" && key !== "passphrase" && value !== "")
    .map(([key, value]) => `${key}=${encode(value)}`);
  if (passphrase) parts.push(`passphrase=${encode(passphrase)}`);
  return createHash("md5").update(parts.join("&")).digest("hex");
}

export function payfastConfig() {
  const merchantId = process.env.PAYFAST_MERCHANT_ID;
  const merchantKey = process.env.PAYFAST_MERCHANT_KEY;
  const passphrase = process.env.PAYFAST_PASSPHRASE;
  const siteUrl = process.env.NEXT_PUBLIC_APP_URL;
  const sandbox = process.env.PAYFAST_SANDBOX !== "false";
  if (!merchantId || !merchantKey || !passphrase || !siteUrl) return null;
  const url = new URL(siteUrl);
  if (url.protocol !== "https:" && url.hostname !== "localhost") return null;
  return { merchantId, merchantKey, passphrase, origin: url.origin, sandbox,
    processUrl: sandbox ? "https://sandbox.payfast.co.za/eng/process" : "https://www.payfast.co.za/eng/process" };
}

export function subscriptionFields(input: {
  merchantId: string; merchantKey: string; origin: string; email: string;
  paymentId: string; plan: Exclude<PlanKey, "TRIAL">;
}) {
  const amount = PLANS[input.plan].monthlyPrice.toFixed(2);
  return {
    merchant_id: input.merchantId,
    merchant_key: input.merchantKey,
    return_url: `${input.origin}/workspace/dashboard?payment=returned`,
    cancel_url: `${input.origin}/workspace/dashboard?payment=cancelled`,
    notify_url: `${input.origin}/api/payfast/itn`,
    email_address: input.email,
    m_payment_id: input.paymentId,
    amount,
    item_name: `Visibility Engine ${PLANS[input.plan].name}`,
    subscription_type: "1",
    recurring_amount: amount,
    frequency: "3",
    cycles: "0",
  };
}
