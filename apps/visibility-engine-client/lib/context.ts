import { headers, cookies } from "next/headers";
import { redirect } from "next/navigation";
import { auth } from "./auth";
import { db } from "./db";
import { AccessError, idSchema } from "./security";

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
export async function scopedSite(
  ctx: Pick<Context, "organisationId">,
  siteId: string,
) {
  const site = await db.site.findFirst({
    where: { id: idSchema.parse(siteId), organisationId: ctx.organisationId },
  });
  if (!site) throw new AccessError(404, "Site not found in this workspace.");
  return site;
}
export async function scopedContent(
  ctx: Pick<Context, "organisationId">,
  contentId: string,
) {
  const content = await db.contentItem.findFirst({
    where: {
      id: idSchema.parse(contentId),
      site: { organisationId: ctx.organisationId },
    },
    include: {
      topic: true,
      site: true,
      approvals: {
        include: { reviewer: true },
        orderBy: { createdAt: "desc" },
      },
      comments: { include: { author: true }, orderBy: { createdAt: "asc" } },
      revisions: { orderBy: { number: "desc" } },
      publications: { orderBy: { createdAt: "desc" } },
    },
  });
  if (!content)
    throw new AccessError(404, "Content not found in this workspace.");
  return content;
}
