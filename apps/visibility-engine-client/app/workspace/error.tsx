"use client";
import Link from "next/link";
export default function ErrorPage({ reset }: { reset: () => void }) {
  return (
    <section className="panel empty">
      <span className="eyebrow">WORKSPACE UNAVAILABLE</span>
      <h1>We couldn’t open this view.</h1>
      <p>
        Check your organisation access and filters, or try again. Your saved
        work is safe.
      </p>
      <button onClick={reset}>Try again</button>
      <Link className="button secondary" href="/workspace/dashboard">
        Return to dashboard
      </Link>
    </section>
  );
}
