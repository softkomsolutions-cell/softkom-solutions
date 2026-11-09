import Link from "next/link";
import { PLANS } from "@/lib/plans";

const tiers = [
  { key: "STARTER" as const, label: "For focused businesses", detail: "Get a clear view of where customers discover your business, and what to improve next.", features: ["Google Search Console + GA4", "AI search visibility observations", "Prioritised opportunities", "Reporting and publishing workflows"] },
  { key: "GROWTH" as const, label: "For growing teams", detail: "Monitor more websites, collaborate across your team, and turn insights into action.", features: ["Everything in Starter", "Expanded site monitoring", "Team collaboration", "More frequent AI visibility checks"] },
  { key: "PRO" as const, label: "For multi-site operations", detail: "Bring multiple brands and websites into one evidence-led visibility programme.", features: ["Everything in Growth", "Multi-brand visibility management", "Larger team access", "Higher-frequency monitoring"] },
];

export default function Pricing() {
  const checkout: Record<string, string | undefined> = {
    STARTER: process.env.BILLING_STARTER_URL,
    GROWTH: process.env.BILLING_GROWTH_URL,
    PRO: process.env.BILLING_PRO_URL,
  };
  return (
    <main className="pricing-page">
      <div className="pricing-nav">
        <Link className="pricing-wordmark" href="/"><span className="pricing-symbol">✳</span><span>Visibility Engine<small>BY SOFTKOM SOLUTIONS</small></span></Link>
        <div className="pricing-nav-actions"><Link href="/">Overview</Link><Link href="/login">Sign in</Link><Link className="pricing-nav-cta" href="/signup">Start free trial <span aria-hidden="true">↗</span></Link></div>
      </div>

      <header className="pricing-hero">
        <div className="pricing-overline"><span className="pricing-overline-dot" /> PLANS & PRICING</div>
        <h1>Know where you stand.<br/><em>Grow where it matters.</em></h1>
        <p>Clearer visibility across Google and AI search. Practical next steps, measurable progress, and a plan that grows with your business.</p>
        <div className="pricing-hero-proof"><span>✓ 14 days free</span><span>✓ No card required for trial</span><span>✓ Start with one website</span></div>
      </header>

      <section className="pricing-plans" aria-label="Subscription plans">
        {tiers.map(({key,label,detail,features}) => {
          const plan = PLANS[key];
          const featured = key === "GROWTH";
          return (
            <article key={key} className={`pricing-tier ${featured ? "pricing-tier-featured" : ""}`}>
              {featured && <div className="pricing-featured-label">MOST POPULAR <span>✦</span></div>}
              <div className="pricing-tier-top"><div className="pricing-tier-name">{plan.name}</div><div className="pricing-tier-audience">{label}</div></div>
              <div className="pricing-price"><span className="pricing-currency">R</span>{plan.monthlyPrice.toLocaleString("en-ZA")}<span className="pricing-period">/ month</span></div>
              <p className="pricing-tier-description">{detail}</p>
              {checkout[key] ? <a className={`pricing-plan-button ${featured ? "pricing-plan-button-primary" : ""}`} href={checkout[key]}>Choose {plan.name}<span aria-hidden="true">↗</span></a> : <Link className={`pricing-plan-button ${featured ? "pricing-plan-button-primary" : ""}`} href={`/signup?plan=${key.toLowerCase()}`}>Start 14-day trial<span aria-hidden="true">↗</span></Link>}
              <div className="pricing-tier-rule" />
              <div className="pricing-included">WHAT IS INCLUDED</div>
              <ul className="pricing-feature-list">
                <li><span className="pricing-check">✓</span>{plan.sites} website{plan.sites === 1 ? "" : "s"}</li>
                <li><span className="pricing-check">✓</span>{plan.members} team members</li>
                <li><span className="pricing-check">✓</span>{plan.aiObservationsPerDay} AI observations / day</li>
                {features.map(feature => <li key={feature}><span className="pricing-check">✓</span>{feature}</li>)}
              </ul>
            </article>
          );
        })}
      </section>

      <section className="pricing-trial-strip">
        <div className="pricing-trial-icon">↗</div>
        <div><span className="pricing-trial-kicker">NOT READY TO CHOOSE?</span><h2>See the evidence for yourself.</h2><p>Explore the platform for 14 days. No payment details required. Your trial includes one website, two members and three AI observations per day.</p></div>
        <Link href="/signup" className="pricing-trial-button">Create free workspace <span aria-hidden="true">→</span></Link>
      </section>

      <section className="pricing-bottom">
        <div className="pricing-bottom-intro"><div className="pricing-overline">THE ESSENTIALS</div><h2>One platform.<br/><em>A clearer picture.</em></h2><p>Every paid plan includes the tools to understand your visibility and act on what the data shows.</p></div>
        <div className="pricing-capabilities">
          <div><span>01 / MEASURE</span><h3>See your search performance</h3><p>Bring Search Console and GA4 evidence into one place.</p></div>
          <div><span>02 / DISCOVER</span><h3>Understand AI visibility</h3><p>Track observations about your brand across AI search.</p></div>
          <div><span>03 / PRIORITISE</span><h3>Know what to improve</h3><p>Find opportunities based on evidence, not guesswork.</p></div>
          <div><span>04 / ACT</span><h3>Turn insight into progress</h3><p>Use reporting and publishing workflows to keep moving.</p></div>
        </div>
      </section>

      <section className="pricing-faq"><div><div className="pricing-overline">GOOD TO KNOW</div><h2>A few things worth knowing.</h2></div><div className="pricing-faq-items"><details><summary>Do I need a credit card to start?</summary><p>No. The 14-day trial does not require payment details.</p></details><details><summary>What happens after the trial?</summary><p>Choose a paid plan to continue using the platform. Your plan determines the number of sites, members and daily AI observations available.</p></details><details><summary>Can I connect Google Analytics and Search Console?</summary><p>Yes. All paid plans include GA4 and Google Search Console connections. You will need permission to access the relevant Google properties.</p></details><details><summary>Need help choosing?</summary><p>Contact <a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a> and we will help you find the right fit.</p></details></div></section>

      <footer className="pricing-footer"><span>© 2026 Softkom Solutions (Pty) Ltd · Visibility Engine</span><div><Link href="/terms">Terms</Link><Link href="/privacy">Privacy</Link><a href="mailto:info@softkomsolutions.com">Contact</a></div></footer>
    </main>
  );
}
