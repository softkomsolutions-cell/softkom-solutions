import Link from "next/link";

export default function Privacy() {
  return (
    <main className="standalone">
      <section className="panel">
        <p className="eyebrow">VISIBILITY ENGINE</p>
        <h1>Privacy notice</h1>
        <p>Visibility Engine is operated by Softkom Solutions (Pty) Ltd. This notice explains the product data used to provide the service.</p>
        <h2>Data we process</h2>
        <p>Account and workspace details, website connection configuration, Search Console and analytics metrics, AI visibility observations, content workflow records, audit events and support information you provide.</p>
        <h2>Why we process it</h2>
        <p>To authenticate users, operate isolated workspaces, connect services you authorise, measure visibility, generate evidence-backed recommendations, publish approved content and support the service.</p>
        <h2>Third-party services</h2>
        <p>When you connect a provider, data may be exchanged with that provider to perform the requested function. AI monitoring uses the configured AI/search provider. Connection credentials are stored securely and are not displayed back to workspace users.</p>
        <h2>Your controls</h2>
        <p>You can disconnect supported integrations from the workspace. For access, correction, export or deletion requests, contact Softkom support.</p>
        <p><a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a></p>
        <Link href="/">Return to Visibility Engine</Link>
      </section>
    </main>
  );
}
