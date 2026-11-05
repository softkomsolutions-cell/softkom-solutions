import { z } from "zod";
import { db } from "./db";
import { scopedSite } from "./scope";
import type { Context } from "./context";
import { ContentStatus } from "@prisma/client";
import { localDay } from "./time";
import { connectionSelect } from "./connections/service";

export type Filters = {
  site?: string;
  month?: string;
  status?: string;
  cluster?: string;
  view?: string;
};
export function monthRange(value?: string) {
  const month = z
    .string()
    .regex(/^(19|20|21)\d{2}-(0[1-9]|1[0-2])$/)
    .parse(value || localDay().slice(0, 7));
  const calendar = new Date(`${month}-01T00:00:00Z`);
  const from = new Date(`${month}-01T00:00:00+02:00`);
  const to = new Date(
    Date.UTC(calendar.getUTCFullYear(), calendar.getUTCMonth() + 1, 1) -
      7200000,
  );
  const previous = new Date(
    Date.UTC(calendar.getUTCFullYear(), calendar.getUTCMonth() - 1, 1) -
      7200000,
  );
  return { month, from, to, previous };
}
export async function workspaceData(
  ctx: Context,
  filters: Filters = {},
  section = "",
) {
  if (filters.site) await scopedSite(ctx, filters.site);
  const range = monthRange(filters.month);
  const site = {
    organisationId: ctx.organisationId,
    ...(filters.site ? { id: filters.site } : {}),
  };
  const status = filters.status
    ? z.nativeEnum(ContentStatus).parse(filters.status)
    : undefined;
  const cluster = filters.cluster
    ? z.string().max(300).parse(filters.cluster)
    : undefined;
  const [
    sites,
    topics,
    content,
    metrics,
    analytics,
    analytics,
    leads,
    observations,
    audits,
    members,
    profile,
  ] = await Promise.all([
    db.site.findMany({
      where: site,
      include: {
        connections: {
          select: { ...connectionSelect, properties: true },
        },
      },
      orderBy: { createdAt: "asc" },
    }),
    db.topic.findMany({
      where: { site, ...(cluster ? { cluster } : {}) },
      include: {
        site: true,
        content: { select: { id: true, title: true, status: true } },
      },
      orderBy: [{ priority: "desc" }, { createdAt: "desc" }],
    }),
    db.contentItem.findMany({
      where: {
        site,
        ...(status ? { status } : {}),
        ...(cluster ? { topic: { cluster } } : {}),
        ...(section === "calendar"
          ? { scheduledAt: { gte: range.from, lt: range.to } }
          : {}),
      },
      include: {
        topic: true,
        site: true,
        publications: { orderBy: { createdAt: "desc" } },
        approvals: { include: { reviewer: { select: { name: true } } } },
      },
      orderBy: [{ scheduledAt: "asc" }, { updatedAt: "desc" }],
    }),
    db.searchMetric.findMany({
      where: { site, date: { gte: range.previous, lt: range.to } },
      orderBy: { date: "desc" },
    }),
    db.analyticsMetric.findMany({
      where: { site, date: { gte: range.previous, lt: range.to } },
      orderBy: { date: "desc" },
    }),
    db.leadAttribution.findMany({
      where: {
        site,
        ...(section === "reports"
          ? { createdAt: { gte: range.from, lt: range.to } }
          : {}),
      },
      include: {
        content: {
          select: {
            title: true,
            id: true,
            topic: { select: { cluster: true } },
          },
        },
      },
      orderBy: { createdAt: "desc" },
    }),
    db.aIObservation.findMany({
      where: { site, observedAt: { gte: range.from, lt: range.to } },
      orderBy: { observedAt: "desc" },
    }),
    db.auditEvent.findMany({
      where: { organisationId: ctx.organisationId },
      take: 20,
      orderBy: { createdAt: "desc" },
    }),
    db.membership.findMany({
      where: { organisationId: ctx.organisationId },
      include: { user: { select: { id: true, name: true, email: true } } },
    }),
    db.businessProfile.findUnique({
      where: { organisationId: ctx.organisationId },
    }),
  ]);
  return {
    sites,
    topics,
    content,
    metrics: metrics.filter(
      (metric) =>
        metric.source !== "GOOGLE_SEARCH_CONSOLE" ||
        (metric.property
          ? sites
              .find((site) => site.id === metric.siteId)
              ?.connections.some(
                (connection) =>
                  connection.provider === "GOOGLE_SEARCH_CONSOLE" &&
                  connection.selectedProperty === metric.property,
              )
          : !sites
              .find((site) => site.id === metric.siteId)
              ?.connections.some(
                (connection) =>
                  connection.provider === "GOOGLE_SEARCH_CONSOLE" &&
                  connection.selectedProperty,
              )),
    ),
    leads,
    observations,
    audits,
    members,
    profile,
    range,
  };
}
export type WorkspaceData = Awaited<ReturnType<typeof workspaceData>>;
export function searchTotals(
  metrics: { clicks: number; impressions: number; position: number | null }[],
) {
  const clicks = metrics.reduce((sum, row) => sum + row.clicks, 0);
  const impressions = metrics.reduce((sum, row) => sum + row.impressions, 0);
  const positioned = metrics.filter((m) => m.position !== null);
  const weight = positioned.reduce((sum, m) => sum + m.impressions, 0);
  return {
    clicks,
    impressions,
    ctr: impressions ? clicks / impressions : null,
    position: weight
      ? positioned.reduce((sum, m) => sum + m.position! * m.impressions, 0) /
        weight
      : null,
  };
}
