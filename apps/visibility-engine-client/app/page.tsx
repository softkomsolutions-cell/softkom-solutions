import Link from "next/link";

const proof = [
  ["Google + GA4", "Real search and analytics evidence"],
  ["AI visibility", "Prompt-level brand and competitor observations"],
  ["Opportunities", "Prioritised actions grounded in measured data"],
  ["Commercial outcomes", "Publishing, leads and revenue attribution"],
];

const workflow = [
  ["01", "Connect", "Bring in your website, Search Console and analytics."],
  ["02", "Measure", "See where customers find you in Google and AI answers."],
  ["03", "Prioritise", "Turn evidence into commercially useful opportunities."],
  ["04", "Act", "Approve, publish and track the work that matters."],
];

export default function Home() {
  return (
    <main className="ve-home">
      <nav className="ve-home-nav">
        <Link href="/" className="pricing-wordmark">
          <span className="pricing-symbol">v.</span>
          <span>
            Visibility Engine
            <small>BY SOFTKOM</small>
          </span>
        </Link>
        <div className="ve-home-nav-actions">
          <Link href="/pricing">Pricing</Link>
          <Link href="/login">Sign in</Link>
          <Link className="ve-nav-cta" href="/signup">Start free trial →</Link>
        </div>
      </nav>

      <section className="ve-hero">
        <div className="ve-hero-copy">
          <span className="ve-kicker"><span /> SEARCH + AI VISIBILITY</span>
          <h1>Know where your next customer can <em>find you</em>.</h1>
          <p className="ve-hero-lede">
            Visibility Engine connects Google, analytics and AI evidence so you can
            see what is working, where competitors are being recommended, and what
            to do next.
          </p>
          <div className="ve-hero-actions">
            <Link className="ve-primary-cta" href="/signup">Start 14-day trial <span>→</span></Link>
            <Link className="ve-secondary-cta" href="/pricing">View plans</Link>
          </div>
          <div className="ve-trust-row">
            <span>No payment details required</span>
            <span>Google + AI evidence</span>
            <span>Built for measurable outcomes</span>
          </div>
        </div>

        <aside className="ve-proof-card" aria-label="Example visibility summary">
          <div className="ve-proof-top">
            <span>VISIBILITY SNAPSHOT</span>
            <strong>Evidence, not guesswork</strong>
          </div>
          <div className="ve-proof-grid">
            <div><span>AI observations</span><strong>06</strong><small>Prompt-level evidence</small></div>
            <div><span>Competitor mentions</span><strong>04</strong><small>Observed in AI answers</small></div>
            <div><span>Search opportunities</span><strong>05</strong><small>Prioritised actions</small></div>
            <div><span>Connected evidence</span><strong>3</strong><small>Search · analytics · AI</small></div>
          </div>
          <div className="ve-proof-footer">
            <span className="ve-proof-dot" />
            Updated from connected workspace data
          </div>
        </aside>
      </section>

      <section className="ve-proof-strip">
        {proof.map(([title, text]) => (
          <div key={title}>
            <strong>{title}</strong>
            <p>{text}</p>
          </div>
        ))}
      </section>

      <section className="ve-story">
        <div className="ve-story-intro">
          <span className="ve-kicker"><span /> FROM SIGNAL TO ACTION</span>
          <h2>Stop collecting dashboards. Start making better visibility decisions.</h2>
          <p>
            Most tools stop at traffic charts. Visibility Engine connects the
            evidence to a clear next action, then keeps the commercial outcome in
            the same workspace.
          </p>
        </div>
        <div className="ve-workflow">
          {workflow.map(([n, title, text]) => (
            <article key={n}>
              <span>{n}</span>
              <h3>{title}</h3>
              <p>{text}</p>
            </article>
          ))}
        </div>
      </section>

      <section className="ve-ai-section">
        <div>
          <span className="ve-kicker"><span /> AI VISIBILITY</span>
          <h2>See who AI recommends when your customers ask for help.</h2>
          <p>
            Visibility Engine records the exact prompt, provider, date, brand
            mentions, page citations and competitors observed. No synthetic
            “AI ranking score” pretending to be more precise than the evidence.
          </p>
          <Link className="ve-inline-link" href="/signup">Measure your visibility →</Link>
        </div>
        <div className="ve-ai-example">
          <span className="ve-example-label">EXAMPLE OBSERVATION</span>
          <p>“Which companies are credible providers of business process automation in South Africa?”</p>
          <div><span>Brand mentioned</span><strong>Not yet</strong></div>
          <div><span>Competitors observed</span><strong>4</strong></div>
          <div><span>Next action</span><strong>Close the visibility gap</strong></div>
        </div>
      </section>

      <section className="ve-final-cta">
        <div>
          <span className="ve-kicker"><span /> START WITH REAL DATA</span>
          <h2>Find the visibility gaps worth fixing first.</h2>
          <p>Start with one site, connect your evidence and see what Visibility Engine finds.</p>
        </div>
        <div className="ve-final-actions">
          <Link className="ve-primary-cta" href="/signup">Start 14-day trial <span>→</span></Link>
          <small>No payment details required.</small>
        </div>
      </section>

      <footer className="ve-home-footer">
        <span>Visibility Engine · Softkom Solutions</span>
        <div>
          <Link href="/pricing">Pricing</Link>
          <Link href="/privacy">Privacy</Link>
          <Link href="/terms">Terms</Link>
          <a href="mailto:info@softkomsolutions.com">Support</a>
        </div>
      </footer>
    </main>
  );
}
