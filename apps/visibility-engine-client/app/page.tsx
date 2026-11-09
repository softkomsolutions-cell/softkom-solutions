import Link from "next/link";

export default function Home() {
  return (
    <main className="standalone">
      <section className="hero-panel">
        <div>
          <span className="eyebrow">VISIBILITY ENGINE</span>
          <h1>Know where your next customer can find you — and where they cannot.</h1>
          <p>Measure Google and AI visibility, find evidence-backed opportunities, publish useful content and track the commercial outcome.</p>
          <div className="form-actions">
            <Link className="button" href="/signup">Start 14-day trial →</Link>
            <Link className="button secondary" href="/pricing">View plans</Link>
            <Link className="inline-link" href="/login">Sign in</Link>
          </div>
        </div>
      </section>
      <section className="panel">
        <p className="eyebrow">WHAT IT DOES</p>
        <h2>Search visibility + AI evidence + commercial action.</h2>
        <div className="kpis">
          <div><strong>Google</strong><p>Search Console and GA4 evidence.</p></div>
          <div><strong>AI</strong><p>Prompt-level brand, citation and competitor observations.</p></div>
          <div><strong>Opportunities</strong><p>Prioritised actions grounded in observed data.</p></div>
          <div><strong>Outcomes</strong><p>Publishing, leads and revenue attribution.</p></div>
        </div>
      </section>
    </main>
  );
}
