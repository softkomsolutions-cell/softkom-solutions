import Link from "next/link";
export default function NotFound() {
  return (
    <main className="standalone panel">
      <p className="eyebrow">NOT FOUND</p>
      <h1>There’s nothing here to open.</h1>
      <p>The record may not exist in your organisation.</p>
      <Link className="button" href="/workspace/dashboard">
        Back to workspace
      </Link>
    </main>
  );
}
