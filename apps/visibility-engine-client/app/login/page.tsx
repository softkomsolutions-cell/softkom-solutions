import Link from "next/link";
import { LoginForm } from "@/components/auth-controls";
export default function Login() {
  return (
    <main className="auth-shell">
      <section className="auth-story">
        <Link href="/" className="brand">
          <span className="brand-mark">v.</span> Visibility Engine
        </Link>
        <p className="eyebrow">MANAGED BY SOFTKOM</p>
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
        <span className="fine">Commercial clarity. Human oversight.</span>
      </section>
      <section className="auth-card">
        <span className="badge">CLIENT WORKSPACE</span>
        <h2>Good to see you.</h2>
        <p>Sign in to review your strategy, content and results.</p>
        <LoginForm />
      </section>
    </main>
  );
}
