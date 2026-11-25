import { headers } from "next/headers";
import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { db } from "@/lib/db";
import { PLANS, planKey } from "@/lib/plans";
import { activatePlan } from "@/app/actions";

export const dynamic = "force-dynamic";

function allowed(email: string) {
  const configured = (process.env.SOFTKOM_ADMIN_EMAILS || "")
    .split(",")
    .map((v) => v.trim().toLowerCase())
    .filter(Boolean);
  return configured.includes(email.toLowerCase());
}

export default async function AdminPage() {
  const session = await auth.api.getSession({ headers: await headers() });
  if (!session) redirect("/login");
  if (!allowed(session.user.email)) redirect("/forbidden");

  const orgs = await db.organisation.findMany({
    include: {
      plan: true,
      users: true,
      sites: true,
    },
    orderBy: { createdAt: "desc" },
  });

  return (
    <main className="standalone">
      <section className="panel">
        <p className="eyebrow">SOFTKOM OPERATOR CONSOLE</p>
        <h1>Customer workspaces</h1>
        <p>Plan activation, usage and tenancy overview. No customer data is edited here.</p>
      </section>
      <section className="panel">
        {orgs.map((org) => {
          const current = planKey(org.plan?.plan);
          return (
            <div className="connection-row" key={org.id}>
              <span>
                <strong>{org.name}</strong>
                <small>{org.sites.length} sites · {org.users.length} members · {org.plan?.status || "No subscription"}</small>
              </span>
              <form action={activatePlan} className="form-actions">
                <input type="hidden" name="organisationId" value={org.id} />
                <select name="plan" defaultValue={current}>
                  {(["TRIAL","STARTER","GROWTH","PRO"] as const).map((key) => (
                    <option key={key} value={key}>{PLANS[key].name}</option>
                  ))}
                </select>
                <button>Activate plan</button>
              </form>
            </div>
          );
        })}
      </section>
    </main>
  );
}
