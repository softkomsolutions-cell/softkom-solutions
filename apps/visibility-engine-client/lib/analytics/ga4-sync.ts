import { createHash, randomUUID } from "node:crypto";
import { db } from "../db";
import { scopedSite } from "../scope";
import type { Context } from "../context";
import { AccessError } from "../security";
import { connectionPermission } from "../connections/service";
import {
  providerError,
  ProviderError,
  type Transport,
} from "../connections/http";
import { GoogleSearchProvider } from "./google";
import { googleAccess } from "./service";
import { MAX_ATTEMPTS, retryAt } from "../publishing/worker";

export function analyticsWindow(now = new Date()) {
  const to = new Date(now.toISOString().slice(0, 10) + "T00:00:00Z");
  to.setUTCDate(to.getUTCDate() - 1);
  const from = new Date(to);
  from.setUTCDate(from.getUTCDate() - 27);
  return { from, to };
}

const grain = (
  siteId: string,
  property: string,
  date: Date,
  landingPage: string,
  source: string,
  medium: string,
) =>
  createHash("sha256")
    .update(
      JSON.stringify([
        siteId,
        property,
        date.toISOString().slice(0, 10),
        landingPage,
        source,
        medium,
        "GOOGLE_ANALYTICS_4",
      ]),
    )
    .digest("hex");

export async function requestAnalyticsSync(ctx: Context, siteId: string) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  return enqueue(siteId, ctx.user.id);
}

async function enqueue(siteId: string, actorId: string) {
  return db.$transaction(
    async (tx) => {
      const c = await tx.siteConnection.findUnique({
        where: {
          siteId_provider: { siteId, provider: "GOOGLE_ANALYTICS_4" },
        },
        include: { site: true },
      });
      if (c?.status !== "CONNECTED" || !c.selectedProperty)
        throw new AccessError(
          409,
          "Connect Google Analytics and select a property first.",
        );
      const previous = await tx.analyticsSync.findUnique({
        where: { connectionId: c.id },
      });
      if (previous && ["PENDING", "PUBLISHING"].includes(previous.status))
        return previous;
      const job = await tx.analyticsSync.upsert({
        where: { connectionId: c.id },
        create: {
          connectionId: c.id,
          ...analyticsWindow(),
          generation: c.generation,
        },
        update: {
          ...analyticsWindow(),
          status: "PENDING",
          generation: c.generation,
          attempts: 0,
          error: null,
          nextRetryAt: null,
          claimToken: null,
          leaseUntil: null,
        },
      });
      await tx.auditEvent.create({
        data: {
          organisationId: c.site.organisationId,
          actorId,
          action: "analytics.sync_requested",
          entityId: job.id,
        },
      });
      return job;
    },
    { isolationLevel: "Serializable" },
  );
}

export async function scheduleAnalyticsSyncs() {
  const connections = await db.siteConnection.findMany({
    where: {
      provider: "GOOGLE_ANALYTICS_4",
      status: "CONNECTED",
      selectedProperty: { not: null },
      OR: [
        { analyticsSync: null },
        {
          analyticsSync: {
            status: "LIVE",
            lastSuccessAt: { lt: new Date(Date.now() - 86400000) },
          },
        },
      ],
    },
  });
  for (const c of connections) await enqueue(c.siteId, "analytics-scheduler");
  return connections.length;
}

export async function processAnalyticsSync(
  id?: string,
  transport?: Transport,
) {
  const now = new Date();
  const candidate = await db.analyticsSync.findFirst({
    where: {
      ...(id ? { id } : {}),
      attempts: { lt: MAX_ATTEMPTS },
      OR: [
        { status: "PENDING" },
        { status: "FAILED", nextRetryAt: { lte: now } },
        { status: "PUBLISHING", leaseUntil: { lt: now } },
      ],
    },
    orderBy: { updatedAt: "asc" },
  });
  if (!candidate) return false;

  const claimToken = randomUUID();
  const claimed = await db.analyticsSync.updateMany({
    where: {
      id: candidate.id,
      status: candidate.status,
      attempts: candidate.attempts,
      claimToken: candidate.claimToken,
    },
    data: {
      status: "PUBLISHING",
      claimToken,
      attempts: { increment: 1 },
      lastAttemptAt: now,
      leaseUntil: new Date(Date.now() + 10 * 60 * 1000),
      nextRetryAt: null,
      error: null,
    },
  });
  if (!claimed.count) return false;

  const job = await db.analyticsSync.findUnique({
    where: { id: candidate.id },
    include: { connection: { include: { site: true } } },
  });
  if (!job) return true;

  const audit = (action: string, code?: string) => ({
    organisationId: job.connection.site.organisationId,
    actorId: "analytics-worker",
    action,
    entityId: job.id,
    ...(code ? { detail: { code } } : {}),
  });

  await db.auditEvent.create({ data: audit("analytics.sync_started") });

  try {
    const { connection, tokens } = await googleAccess(
      job.connectionId,
      transport,
    );
    if (
      connection.generation !== job.generation ||
      !connection.selectedProperty
    )
      throw new ProviderError(
        "changed",
        "Google Analytics property changed. Request a new sync.",
      );

    const provider = new GoogleSearchProvider(transport);
    const properties = await provider.analyticsProperties(tokens.access_token);
    if (
      !properties.some((p) => p.property === connection.selectedProperty)
    )
      throw new ProviderError(
        "property",
        "Selected Google Analytics property is no longer accessible.",
      );

    const rows = await provider.fetchAnalytics(
      tokens.access_token,
      connection.selectedProperty,
      job.from,
      job.to,
    );

    await db.$transaction(
      async (tx) => {
        await tx.$queryRaw`SELECT id FROM "SiteConnection" WHERE id=${connection.id} FOR UPDATE`;
        const current = await tx.siteConnection.findUnique({
          where: { id: connection.id },
        });
        if (
          current?.status !== "CONNECTED" ||
          current.generation !== job.generation
        )
          throw new ProviderError(
            "changed",
            "Analytics connection changed during sync.",
          );

        const done = await tx.analyticsSync.updateMany({
          where: { id: job.id, status: "PUBLISHING", claimToken },
          data: {
            status: "LIVE",
            lastSuccessAt: new Date(),
            latestDataDate: rows.length
              ? new Date(
                  rows.reduce(
                    (latest, row) => Math.max(latest, row.date.getTime()),
                    0,
                  ),
                )
              : null,
            claimToken: null,
            leaseUntil: null,
            nextRetryAt: null,
            error: null,
          },
        });
        if (!done.count) return;

        await tx.analyticsMetric.deleteMany({
          where: {
            siteId: connection.siteId,
            property: connection.selectedProperty,
            date: { gte: job.from, lte: job.to },
          },
        });

        for (const row of rows) {
          const grainKey = grain(
            connection.siteId,
            connection.selectedProperty!,
            row.date,
            row.landingPage,
            row.source,
            row.medium,
          );
          await tx.analyticsMetric.create({
            data: {
              siteId: connection.siteId,
              property: connection.selectedProperty!,
              ...row,
              grainKey,
            },
          });
        }

        await tx.siteConnection.update({
          where: { id: connection.id },
          data: { lastSyncedAt: new Date(), error: null },
        });
        await tx.auditEvent.create({
          data: audit("analytics.sync_completed"),
        });
      },
      { timeout: 120000 },
    );
  } catch (error) {
    const safe = providerError(error);
    if (["authentication", "oauth", "property"].includes(safe.code))
      await db.siteConnection.updateMany({
        where: {
          id: job.connectionId,
          generation: job.generation,
        },
        data: { status: "ERROR", error: safe.message },
      });

    await db.$transaction(async (tx) => {
      const changed = await tx.analyticsSync.updateMany({
        where: {
          id: job.id,
          claimToken,
          status: "PUBLISHING",
        },
        data: {
          status: "FAILED",
          error: safe.message,
          nextRetryAt:
            safe.retryable && job.attempts < MAX_ATTEMPTS
              ? retryAt(job.attempts)
              : null,
          claimToken: null,
          leaseUntil: null,
        },
      });
      if (changed.count)
        await tx.auditEvent.create({
          data: audit("analytics.sync_failed", safe.code),
        });
    });
  }
  return true;
}
