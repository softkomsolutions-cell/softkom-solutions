import Link from "next/link";
import { PLANS } from "@/lib/plans";

const paid = (["STARTER", "GROWTH", "PRO"] as const).map((key) => [key, PLANS[key]] as const);

export default function Pricing() {
  const links: Record<string, string | undefined> = {
    STARTER: process.env.BILLING_STARTER_URL,
    GROWTH: process.env.BILLING_GROWTH_URL,
    PRO: process.env.BILLING_PRO_URL,
  };
  return (
    <main className="standalone">
      <section className="panel">
        <p className="eyebrow">VISIBILITY ENGINE PLANS</p>
        <h1>Start with evidence. Upgrade when you need more reach.</h1>
        <p>All paid plans include Search Console, GA4, AI visibility monitoring, opportunities, reports and publishing workflows.</p>
      </section>
      <section className="topic-grid">
        {paid.map(([key, plan]) => (
          <article className="panel" key={key}>
            <p className="eyebrow">{plan.name}</p>
            <h2>R{plan.monthlyPrice.toLocaleString("en-ZA")}/month</h2>
            <p>{plan.sites} site{plan.sites === 1 ? "" : "s"} · {plan.members} members · {plan.aiObservationsPerDay} AI observations/day</p>
            {links[key] ? (
              <a className="button" href={links[key]}>Choose {plan.name} →</a>
            ) : (
              <Link className="button" href="/signup">Start trial →</Link>
            )}
          </article>
        ))}
      </section>
      <section className="panel">
        <h2>14-day trial</h2>
        <p>No payment details required. Trial limits: 1 site, 2 members and 3 AI observations per day. Paid plans can be activated through connected checkout links or by Softkom after invoicing.</p>
        <Link className="button" href="/signup">Create trial workspace →</Link>
      </section>
    </main>
  );
}
