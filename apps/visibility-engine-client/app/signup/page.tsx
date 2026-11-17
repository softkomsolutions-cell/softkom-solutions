import Link from "next/link";
import { SignupForm } from "@/components/auth-controls";

export default async function Signup({
  searchParams,
}: {
  searchParams: Promise<{ invite?: string; plan?: string }>;
}) {
  const { invite = "", plan = "" } = await searchParams;
  const selectedPlan = ["starter", "growth", "pro"].includes(plan.toLowerCase()) ? plan.toLowerCase() : "";
  return (
    <main className="auth-shell">
      <section className="auth-story">
        <Link href="/" className="brand">
          <span className="brand-mark">v.</span> Visibility Engine
        </Link>
        <p className="eyebrow">AI + SEARCH VISIBILITY</p>
        <h1>Know where to focus next.</h1>
        <p>Connect your website, measure search and AI visibility, and turn evidence into practical opportunities.</p>
        <ol className="journey">
          <li>Connect your website and Google data</li>
          <li>Measure AI and search visibility</li>
          <li>Act on evidence-backed opportunities</li>
        </ol>
        <span className="fine">14-day trial · no payment details required.</span>
      </section>
      <section className="auth-card">
        <span className="badge">{invite ? "JOIN WORKSPACE" : "START YOUR TRIAL"}</span>
        <h2>{invite ? "Create your account." : "Create your workspace."}</h2>
        <p>{invite ? "Use the invited email address. You will join the workspace after signup." : "You can connect your services after the four-step setup."}</p>
        {!invite && selectedPlan && <p className="fine">Selected plan: <strong>{selectedPlan.charAt(0).toUpperCase() + selectedPlan.slice(1)}</strong>. Your 14-day trial starts with trial limits; you can choose a paid subscription after setup.</p>}
        <SignupForm inviteToken={invite} selectedPlan={selectedPlan} />
        <p className="fine">Already have an account? <Link href={invite ? `/login?invite=${encodeURIComponent(invite)}` : "/login"}>Sign in</Link>.</p>
      </section>
    </main>
  );
}
