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
export function syncWindow(now = new Date()) {
  // Conservative three-day lag; Google finalised data may still omit anonymised
  // queries and low-volume rows. Dates are calendar dates, not local timestamps.
  const to = new Date(now.toISOString().slice(0, 10) + "T00:00:00Z");
  to.setUTCDate(to.getUTCDate() - 3);
  const from = new Date(to);
  from.setUTCDate(from.getUTCDate() - 27);
  return { from, to };
}
export const grain = (
  siteId: string,
  date: Date,
  query: string,
  page: string,
  property = "",
) =>
  createHash("sha256")
    .update(
      JSON.stringify([
        siteId,
        date.toISOString().slice(0, 10),
        query,
        page,
        "GOOGLE_SEARCH_CONSOLE",
        property,
      ]),
    )
    .digest("hex");
export async function requestSync(ctx: Context, siteId: string) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  return enqueue(siteId, ctx.user.id);
}
async function enqueue(siteId: string, actorId: string) {
  return db.$transaction(
    async (tx) => {
      const c = await tx.siteConnection.findUnique({
        where: {
          siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" },
        },
        include: { site: true },
      });
      if (c?.status !== "CONNECTED" || !c.selectedProperty)
        throw new AccessError(
          409,
          "Connect Google and select a property first.",
        );
      const previous = await tx.searchSync.findUnique({
        where: { connectionId: c.id },
      });
      if (previous && ["PENDING", "PUBLISHING"].includes(previous.status))
        return previous;
      const job = await tx.searchSync.upsert({
        where: { connectionId: c.id },
        create: {
          connectionId: c.id,
          ...syncWindow(),
          generation: c.generation,
        },
        update: {
          ...syncWindow(),
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
          action: "search.sync_requested",
          entityId: job.id,
        },
      });
      return job;
    },
    { isolationLevel: "Serializable" },
  );
}
export async function scheduleSyncs() {
  const connections = await db.siteConnection.findMany({
    where: {
      provider: "GOOGLE_SEARCH_CONSOLE",
      status: "CONNECTED",
      selectedProperty: { not: null },
      OR: [
        { sync: null },
        {
          sync: {
            status: "LIVE",
            lastSuccessAt: { lt: new Date(Date.now() - 86400000) },
          },
        },
      ],
    },
  });
  for (const c of connections) await enqueue(c.siteId, "search-scheduler");
  return connections.length;
}
export async function processSync(id?: string, transport?: Transport) {
  const now = new Date();
  const exhausted = await db.searchSync.findMany({
    where: {
      ...(id ? { id } : {}),
      status: "PUBLISHING",
      attempts: { gte: MAX_ATTEMPTS },
      leaseUntil: { lt: now },
    },
    take: 20,
    include: {
      connection: { include: { site: { select: { organisationId: true } } } },
    },
  });
  for (const stale of exhausted)
    await db.$transaction(async (tx) => {
      const result = await tx.searchSync.updateMany({
        where: {
          id: stale.id,
          status: "PUBLISHING",
          claimToken: stale.claimToken,
          leaseUntil: { lt: now },
        },
        data: {
          status: "FAILED",
          error:
            "The final sync attempt expired. Request a new sync after checking the connection.",
          claimToken: null,
          leaseUntil: null,
          nextRetryAt: null,
        },
      });
      if (result.count)
        await tx.auditEvent.create({
          data: {
            organisationId: stale.connection.site.organisationId,
            actorId: "search-worker",
            action: "search.sync_failed",
            entityId: stale.id,
            detail: { code: "lease_exhausted" },
          },
        });
    });
  const candidate = await db.searchSync.findFirst({
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
  const claimed = await db.searchSync.updateMany({
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
  const job = await db.searchSync.findUnique({
    where: { id: candidate.id },
    include: { connection: { include: { site: true } } },
  });
  if (!job) return true;
  const audit = (action: string, detail?: { code: string }) => ({
    organisationId: job.connection.site.organisationId,
    actorId: "search-worker",
    action,
    entityId: job.id,
    ...(detail ? { detail } : {}),
  });
  await db.auditEvent.create({ data: audit("search.sync_started") });
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
        "Search Console property changed. Request a new sync.",
      );
    const provider = new GoogleSearchProvider(transport);
    if (
      !(await provider.properties(tokens.access_token)).some(
        (p) => p.siteUrl === connection.selectedProperty,
      )
    )
      throw new ProviderError(
        "property",
        "Selected Search Console property is no longer accessible. Reconnect or select another property.",
      );
    const rows = await provider.fetch(
      tokens.access_token,
      connection.selectedProperty,
      job.from,
      job.to,
    );
    await db.$transaction(
      async (tx) => {
        // Row lock serialises completion with disconnect and property selection.
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
            "Connection changed during sync. Request a new sync.",
          );
        const done = await tx.searchSync.updateMany({
          where: { id: job.id, status: "PUBLISHING", claimToken },
          data: {
            status: "LIVE",
            lastSuccessAt: new Date(),
            lastFrom: job.from,
            lastTo: job.to,
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
            error: null,
            nextRetryAt: null,
          },
        });
        if (!done.count) return;
        // Replace only the selected site's GSC range, atomically. This also removes
        // observations withdrawn by Google; manual and other-provider data remain.
        await tx.searchMetric.deleteMany({
          where: {
            siteId: connection.siteId,
            source: "GOOGLE_SEARCH_CONSOLE",
            property: connection.selectedProperty,
            date: { gte: job.from, lte: job.to },
          },
        });
        for (const row of rows)
          await tx.searchMetric.upsert({
            where: {
              grainKey: grain(
                connection.siteId,
                row.date,
                row.query,
                row.pageUrl,
                connection.selectedProperty!,
              ),
            },
            create: {
              siteId: connection.siteId,
              ...row,
              source: "GOOGLE_SEARCH_CONSOLE",
              property: connection.selectedProperty,
              grainKey: grain(
                connection.siteId,
                row.date,
                row.query,
                row.pageUrl,
                connection.selectedProperty!,
              ),
            },
            update: row,
          });
        await tx.siteConnection.update({
          where: { id: connection.id },
          data: { lastSyncedAt: new Date(), error: null },
        });
        await tx.auditEvent.create({ data: audit("search.sync_completed") });
      },
      { timeout: 120000 },
    );
  } catch (error) {
    const safe = providerError(error);
    if (["authentication", "oauth", "property"].includes(safe.code))
      await db.$transaction(async (tx) => {
        const changed = await tx.siteConnection.updateMany({
          where: { id: job.connectionId, generation: job.generation },
          data: { status: "ERROR", error: safe.message },
        });
        if (changed.count)
          await tx.auditEvent.create({
            data: {
              ...audit("connection.failed", { code: safe.code }),
              entityId: job.connectionId,
            },
          });
      });
    await db.$transaction(async (tx) => {
      const changed = await tx.searchSync.updateMany({
        where: { id: job.id, claimToken, status: "PUBLISHING" },
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
          data: audit("search.sync_failed", { code: safe.code }),
        });
    });
  }
  return true;
}
