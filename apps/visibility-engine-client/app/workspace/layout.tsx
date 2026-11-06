import Link from "next/link";
import type { ReactNode } from "react";
import { getContext } from "@/lib/context";
import { Navigation } from "@/components/navigation";
import { Logout } from "@/components/auth-controls";
import { switchOrganisation } from "@/app/actions";
import { Feedback } from "@/components/feedback";
export const dynamic = "force-dynamic";
export default async function WorkspaceLayout({
  children,
}: {
  children: ReactNode;
}) {
  const ctx = await getContext();
  return (
    <div className="shell">
      <a className="skip-link" href="#main-content">
        Skip to content
      </a>
      <aside className="sidebar">
        <Link href="/workspace/dashboard" className="brand">
          <span className="brand-mark">v.</span>
          <span>
            Visibility<span className="brand-sub">ENGINE / SOFTKOM</span>
          </span>
        </Link>
        <form action={switchOrganisation} className="org-switch">
          <label htmlFor="organisationId">YOUR WORKSPACE</label>
          <select
            id="organisationId"
            name="organisationId"
            defaultValue={ctx.organisationId}
          >
            {ctx.memberships.map((m) => (
              <option key={m.id} value={m.organisationId}>
                {m.organisation.name}
              </option>
            ))}
          </select>
          {ctx.memberships.length > 1 && (
            <button className="text-button">Switch workspace</button>
          )}
        </form>
        <Navigation />
        <div className="managed">
          <span className="dot" /> Managed by Softkom
          <p>Strategy. Content. Commercial outcomes.</p>
        </div>
      </aside>
      <div className="main-shell">
        <div className="topbar">
          <span>
            Client workspace <span className="separator">/</span>{" "}
            <strong>{ctx.organisation.name}</strong>
          </span>
          <div className="user-menu">
            <span className="avatar">
              {(ctx.user.name || ctx.user.email).slice(0, 1).toUpperCase()}
            </span>
            <span>
              {ctx.user.name || ctx.user.email}
              <small>{ctx.role.replaceAll("_", " ").toLowerCase()}</small>
            </span>
            <Logout />
          </div>
        </div>
        <main id="main-content" className="content">
          <Feedback />
          {children}
        </main>
        <footer>
          Visibility Engine <span>Thoughtfully managed by Softkom.</span>
        </footer>
      </div>
    </div>
  );
}
