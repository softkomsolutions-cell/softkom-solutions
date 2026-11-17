import Link from "next/link";
import { LoginForm } from "@/components/auth-controls";

export default async function Login({
  searchParams,
}: {
  searchParams: Promise<{ invite?: string }>;
}) {
  const { invite = "" } = await searchParams;
  return (
    <main className="auth-shell">
      <section className="auth-story">
        <Link href="/" className="brand">
          <span className="brand-mark">v.</span> Visibility Engine
        </Link>
        <p className="eyebrow">AI + SEARCH VISIBILITY</p>
        <h1>
          Visibility that
          <br />
          means business.
        </h1>
        <p>A clear line from the right opportunity to your next customer.</p>
        <ol className="journey">
          <li>Find the opportunity</li>
          <li>Approve the right content</li>
          <li>Understand the outcome</li>
        </ol>
        <span className="fine">Commercial clarity. Transparent evidence.</span>
      </section>
      <section className="auth-card">
        <span className="badge">{invite ? "WORKSPACE INVITATION" : "CLIENT WORKSPACE"}</span>
        <h2>Good to see you.</h2>
        <p>{invite ? "Sign in with the invited email address to join the workspace." : "Sign in to review your strategy, content and results."}</p>
        <LoginForm inviteToken={invite} />
      </section>
    </main>
  );
}
