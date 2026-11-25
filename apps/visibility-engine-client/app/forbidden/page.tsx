import Link from "next/link";
export default function Forbidden() {
  return (
    <main className="standalone panel">
      <span className="eyebrow">ACCESS RESTRICTED</span>
      <h1>This workspace isn’t available to your account.</h1>
      <p>Ask your Softkom manager to check your organisation membership.</p>
      <Link className="button" href="/workspace/dashboard">
        Return to your workspace
      </Link>
    </main>
  );
}
