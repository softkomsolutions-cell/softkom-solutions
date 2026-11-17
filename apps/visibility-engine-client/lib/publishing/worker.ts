import { randomUUID } from "node:crypto";
import { db } from "../db";
import { decryptCredential } from "../connections/credentials";
import {
  providerError,
  ProviderError,
  destination,
  type Transport,
} from "../connections/http";
import { WordPressProvider } from "./wordpress";
import type { PublishInput } from "./providers";
import { scopedContent } from "../scope";
import type { Context } from "../context";
import { requireManager, AccessError } from "../security";
import { event } from "../connections/service";
export const MAX_ATTEMPTS = 5;
export const retryAt = (attempt: number, now = new Date()) =>
  new Date(now.getTime() + Math.min(3600, 30 * 2 ** (attempt - 1)) * 1000);
export async function processPublication(id?: string, transport?: Transport) {
  const now = new Date();
  // A process can die during its final allowed attempt. Expire that lease into
  // a visible dead-letter state rather than leaving PUBLISHING forever.
  const exhausted = await db.publication.findMany({
    where: {
      ...(id ? { id } : {}),
      status: "PUBLISHING",
      attempts: { gte: MAX_ATTEMPTS },
      leaseUntil: { lt: now },
    },
    take: 20,
    include: { site: { select: { organisationId: true } } },
  });
  for (const stale of exhausted)
    await db.$transaction(async (tx) => {
      const result = await tx.publication.updateMany({
        where: {
          id: stale.id,
          status: "PUBLISHING",
          claimToken: stale.claimToken,
          leaseUntil: { lt: now },
        },
        data: {
          status: "FAILED",
          error:
            "The final worker attempt expired. Manual recovery is required.",
          claimToken: null,
          leaseUntil: null,
          nextRetryAt: null,
        },
      });
      if (result.count)
        await tx.auditEvent.create({
          data: {
            organisationId: stale.site.organisationId,
            actorId: "publication-worker",
            action: "publication.failed",
            entityId: stale.id,
            detail: { code: "lease_exhausted" },
          },
        });
    });
  const candidate = await db.publication.findFirst({
    where: {
      ...(id ? { id } : {}),
      attempts: { lt: MAX_ATTEMPTS },
      OR: [
        {
          status: "PENDING",
          OR: [{ nextRetryAt: null }, { nextRetryAt: { lte: now } }],
        },
        { status: "FAILED", nextRetryAt: { lte: now } },
        { status: "PUBLISHING", leaseUntil: { lt: now } },
      ],
    },
    orderBy: { createdAt: "asc" },
  });
  if (!candidate) return false;
  const claimToken = randomUUID();
  const claimed = await db.$transaction(async (tx) => {
    const update = await tx.publication.updateMany({
      where: {
        id: candidate.id,
        status: candidate.status,
        attempts: candidate.attempts,
        claimToken: candidate.claimToken,
      },
      data: {
        status: "PUBLISHING",
        claimToken,
        leaseUntil: new Date(now.getTime() + 10 * 60 * 1000),
        lastAttemptAt: now,
        attempts: { increment: 1 },
        nextRetryAt: null,
        error: null,
      },
    });
    if (!update.count) return false;
    const site = await tx.site.findUniqueOrThrow({
      where: { id: candidate.siteId },
    });
    await tx.auditEvent.create({
      data: {
        organisationId: site.organisationId,
        actorId: "publication-worker",
        action: "publication.claimed",
        entityId: candidate.id,
      },
    });
    if (candidate.attempts > 0)
      await tx.auditEvent.create({
        data: {
          organisationId: site.organisationId,
          actorId: "publication-worker",
          action: "publication.retried",
          entityId: candidate.id,
        },
      });
    return true;
  });
  if (!claimed) return false;
  const p = await db.publication.findUniqueOrThrow({
    where: { id: candidate.id },
    include: {
      site: true,
      content: {
        include: {
          revisions: true,
          approvals: { orderBy: { createdAt: "desc" } },
        },
      },
    },
  });
  const audit = (action: string, detail?: { code: string }) => ({
    organisationId: p.site.organisationId,
    actorId: "publication-worker",
    action,
    entityId: p.id,
    ...(detail ? { detail } : {}),
  });
  try {
    const revision = p.content.revisions.find(
      (r) => r.number === p.requestedRevision,
    );
    const approval = p.content.approvals.find(
      (a) => a.revision === p.requestedRevision,
    );
    if (!revision || (!p.overrideReason && approval?.decision !== "APPROVED"))
      throw new ProviderError(
        "approval",
        "The requested revision is not approved. Request a new approval.",
      );
    if (
      !revision.title.trim() ||
      !revision.body.trim() ||
      !revision.metaTitle ||
      !revision.metaDesc ||
      !revision.funnel ||
      !revision.slug
    )
      throw new ProviderError(
        "validation",
        "The approved revision needs content, metadata and a funnel destination.",
      );
    const connection = await db.siteConnection.findUnique({
      where: { siteId_provider: { siteId: p.siteId, provider: "WORDPRESS" } },
    });
    if (
      !connection?.encryptedCredentials ||
      connection.status !== "CONNECTED" ||
      !connection.verifiedAt
    )
      throw new ProviderError(
        "connection",
        "WordPress requires a verified connection before publishing.",
      );
    const credentials = JSON.parse(
      decryptCredential(connection.encryptedCredentials),
    );
    if (
      p.liveUrl &&
      new URL(p.liveUrl).origin !== new URL(credentials.siteUrl).origin
    )
      throw new ProviderError(
        "connection",
        "The remote publication belongs to the previous WordPress site. Reconnect that site before retrying.",
      );
    const funnel = new URL(revision.funnel, credentials.siteUrl + "/");
    destination(funnel.href);
    if (funnel.origin !== new URL(credentials.siteUrl).origin)
      throw new ProviderError(
        "funnel",
        "The funnel destination must belong to the connected website.",
      );
    const input: PublishInput = {
      title: revision.title,
      html: revision.body,
      slug: revision.slug,
      metaTitle: revision.metaTitle,
      metaDescription: revision.metaDesc,
      funnel: funnel.href,
      idempotencyKey: p.id,
      ...(p.externalId ? { externalId: p.externalId } : {}),
    };
    const provider = new WordPressProvider(credentials, transport);
    const result = await provider.publish(input);
    // Save remote identity before public verification, retaining it on failure.
    const checkpoint = await db.publication.updateMany({
      where: { id: p.id, claimToken, status: "PUBLISHING" },
      data: {
        externalId: result.externalId,
        liveUrl: result.liveUrl,
        providerResponse: result.response,
        publishedAt: result.publishedAt,
      },
    });
    if (!checkpoint.count) return true;
    await provider.verifyPublication(result, input);
    await db.$transaction(async (tx) => {
      const current = await tx.siteConnection.findUnique({
        where: { id: connection.id },
      });
      if (
        current?.generation !== connection.generation ||
        current.status !== "CONNECTED"
      )
        throw new ProviderError(
          "connection",
          "Connection changed during publication. Verify before retrying.",
        );
      const update = await tx.publication.updateMany({
        where: { id: p.id, claimToken, status: "PUBLISHING" },
        data: {
          status: "LIVE",
          verifiedAt: new Date(),
          verificationError: null,
          error: null,
          claimToken: null,
          leaseUntil: null,
        },
      });
      if (!update.count) return;
      await tx.contentItem.updateMany({
        where: { id: p.contentId, revision: p.requestedRevision },
        data: { status: "PUBLISHED", publishedAt: result.publishedAt },
      });
      if (p.content.topicId)
        await tx.topic.updateMany({
          where: { id: p.content.topicId, siteId: p.siteId },
          data: { status: "PUBLISHED" },
        });
      await tx.auditEvent.create({ data: audit("publication.live") });
    });
  } catch (error) {
    const safe = providerError(error);
    await db.$transaction(async (tx) => {
      const updated = await tx.publication.updateMany({
        where: { id: p.id, claimToken, status: "PUBLISHING" },
        data: {
          status: "FAILED",
          error: safe.message,
          verificationError: safe.code === "verification" ? safe.message : null,
          nextRetryAt:
            safe.retryable && p.attempts < MAX_ATTEMPTS
              ? retryAt(p.attempts)
              : null,
          claimToken: null,
          leaseUntil: null,
        },
      });
      if (updated.count)
        await tx.auditEvent.create({
          data: audit("publication.failed", { code: safe.code }),
        });
    });
  }
  return true;
}
export async function retryPublication(ctx: Context, id: string) {
  requireManager(ctx.role);
  const p = await db.publication.findFirst({
    where: { id, site: { organisationId: ctx.organisationId } },
  });
  if (!p) throw new AccessError(404, "Publication not found.");
  await scopedContent(ctx, p.contentId);
  await db.$transaction(async (tx) => {
    const update = await tx.publication.updateMany({
      where: { id: p.id, status: "FAILED", attempts: { lt: MAX_ATTEMPTS } },
      data: { status: "PENDING", nextRetryAt: null, error: null },
    });
    if (!update.count)
      throw new AccessError(
        409,
        "Publication cannot retry. Maximum attempts reached or another worker is processing it.",
      );
    await tx.auditEvent.create({
      data: event(ctx, "publication.retried", p.id),
    });
  });
}
