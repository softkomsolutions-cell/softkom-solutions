import Link from "next/link";

export default function Terms() {
  return (
    <main className="standalone">
      <section className="panel">
        <p className="eyebrow">VISIBILITY ENGINE</p>
        <h1>Service terms</h1>
        <p>Visibility Engine is a software service operated by Softkom Solutions (Pty) Ltd. By creating a workspace you agree to use the service lawfully and only with websites, accounts and data you are authorised to manage.</p>
        <h2>Trial and plans</h2>
        <p>Trial access is limited in duration and usage. Paid plan limits are shown on the pricing page and in your workspace. Access to write actions may be restricted when a trial or subscription ends.</p>
        <h2>Evidence, not guarantees</h2>
        <p>Search, analytics and AI observations reflect the connected sources and measured periods. Recommendations are decision support, not guarantees of ranking, traffic, leads or revenue.</p>
        <h2>Publishing</h2>
        <p>Content is published only through connected destinations and the approval workflow. You remain responsible for the accuracy, legality and suitability of content representing your organisation.</p>
        <h2>Availability and changes</h2>
        <p>Features and supported providers may change as the product develops. Material account or plan changes will be reflected in the service or communicated to the workspace owner.</p>
        <h2>Support</h2>
        <p><a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a></p>
        <Link href="/">Return to Visibility Engine</Link>
      </section>
    </main>
  );
}
