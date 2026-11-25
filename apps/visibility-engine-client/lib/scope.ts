import { db } from "./db";
import { AccessError, idSchema } from "./security";
import type { Context } from "./context";
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
