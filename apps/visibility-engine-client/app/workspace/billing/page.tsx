import Link from "next/link";
import { getContext } from "@/lib/context";
import { db } from "@/lib/db";
import { PLANS } from "@/lib/plans";
import { canManage } from "@/lib/security";
import { CheckoutButton } from "@/components/payfast-checkout";

export const dynamic = "force-dynamic";

export default async function BillingPage() {
  const ctx = await getContext();
  const subscription = await db.subscription.findUnique({
    where: { organisationId: ctx.organisationId },
  });
  const configured = !!(
    process.env.PAYFAST_MERCHANT_ID &&
    process.env.PAYFAST_MERCHANT_KEY &&
    process.env.PAYFAST_PASSPHRASE &&
    process.env.NEXT_PUBLIC_APP_URL
  );
  return (
    <main style={{ maxWidth: 760, margin: "4rem auto", padding: "1rem" }}>
      <Link href="/workspace/dashboard">← Workspace</Link>
      <h1>Subscription and billing</h1>
      <p>Current plan: <strong>{subscription?.plan ?? "TRIAL"}</strong></p>
      <p>Status: <strong>{subscription?.status ?? "TRIALING"}</strong></p>
      {subscription?.trialEndsAt && <p>Trial ends: {subscription.trialEndsAt.toLocaleDateString("en-ZA")}</p>}
      {!configured && <p role="status">Secure payments are not yet configured. Your free trial remains available until its expiry.</p>}
      {configured && canManage(ctx.role) && (
        <section>
          <h2>Choose a monthly plan</h2>
          {(["STARTER", "GROWTH", "PRO"] as const).map((plan) => (
            <div key={plan}>
              <h3>{PLANS[plan].name} — R{PLANS[plan].monthlyPrice.toLocaleString("en-ZA")} / month</h3>
              <CheckoutButton plan={plan} />
            </div>
          ))}
          <p>Payment is completed on PayFast. Plan changes take effect only after payment confirmation.</p>
        </section>
      )}
      {!canManage(ctx.role) && <p>Ask your workspace owner or administrator to manage billing.</p>}
    </main>
  );
}
