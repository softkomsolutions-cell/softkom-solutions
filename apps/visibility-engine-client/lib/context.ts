import { headers, cookies } from "next/headers";
import { redirect } from "next/navigation";
import { auth } from "./auth";
import { db } from "./db";

export async function currentUser() {
  const session = await auth.api.getSession({ headers: await headers() });
  if (!session) redirect("/login");
  return session.user;
}
export async function getContext() {
  const user = await currentUser();
  const memberships = await db.membership.findMany({
    where: { userId: user.id },
    include: { organisation: true },
    orderBy: { id: "asc" },
  });
  if (!memberships.length) redirect("/onboarding");
  const selected = (await cookies()).get("visibility-org")?.value;
  const membership = selected
    ? memberships.find((m) => m.organisationId === selected)
    : memberships[0];
  if (!membership) redirect("/forbidden");
  return {
    user,
    memberships,
    organisation: membership.organisation,
    organisationId: membership.organisationId,
    role: membership.role,
  };
}
export type Context = Awaited<ReturnType<typeof getContext>>;
export { scopedSite, scopedContent } from "./scope";
