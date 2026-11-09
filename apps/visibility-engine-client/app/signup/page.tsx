import Link from "next/link";
import { SignupForm } from "@/components/auth-controls";

export default function Signup() {
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
        <span className="badge">START YOUR TRIAL</span>
        <h2>Create your workspace.</h2>
        <p>You can connect your services after the four-step setup.</p>
        <SignupForm />
        <p className="fine">Already have an account? <Link href="/login">Sign in</Link>.</p>
      </section>
    </main>
  );
}
