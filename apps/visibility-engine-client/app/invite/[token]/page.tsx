import Link from "next/link";
import { headers } from "next/headers";
import { auth } from "@/lib/auth";
import { db } from "@/lib/db";
import { invitationHash } from "@/lib/saas";
import { acceptInvitation } from "@/app/actions";

export const dynamic = "force-dynamic";

export default async function InvitePage({ params }: { params: Promise<{ token: string }> }) {
  const { token } = await params;
  const invite = await db.invitation.findUnique({
    where: { tokenHash: invitationHash(token) },
    include: { organisation: true },
  });
  const valid = !!invite && !invite.acceptedAt && invite.expiresAt > new Date();
  const session = await auth.api.getSession({ headers: await headers() });

  if (!valid) {
    return <main className="standalone"><section className="panel"><h1>Invitation unavailable</h1><p>This invitation has expired or has already been used.</p><Link href="/login">Sign in</Link></section></main>;
  }

  if (!session) {
    return (
      <main className="standalone">
        <section className="panel">
          <p className="eyebrow">WORKSPACE INVITATION</p>
          <h1>Join {invite.organisation.name}</h1>
          <p>This invitation was sent to <strong>{invite.email}</strong>.</p>
          <div className="form-actions">
            <Link className="button" href={`/signup?invite=${encodeURIComponent(token)}`}>Create account</Link>
            <Link className="button secondary" href="/login">Sign in</Link>
          </div>
        </section>
      </main>
    );
  }

  if (session.user.email.toLowerCase() !== invite.email.toLowerCase()) {
    return <main className="standalone"><section className="panel"><h1>Use the invited account</h1><p>You are signed in as {session.user.email}, but this invitation is for {invite.email}.</p><Link href="/login">Switch account</Link></section></main>;
  }

  return (
    <main className="standalone">
      <section className="panel">
        <p className="eyebrow">WORKSPACE INVITATION</p>
        <h1>Join {invite.organisation.name}</h1>
        <p>You will join as {invite.role.replaceAll("_", " ").toLowerCase()}.</p>
        <form action={acceptInvitation}>
          <input type="hidden" name="token" value={token} />
          <button>Accept invitation →</button>
        </form>
      </section>
    </main>
  );
}
