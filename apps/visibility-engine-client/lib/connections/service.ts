import { z } from "zod";
import { db } from "../db";
import { scopedSite } from "../scope";
import type { Context } from "../context";
import { AccessError, idSchema, text, requireManager } from "../security";
import { encryptCredential, decryptCredential } from "./credentials";
import { destination, providerError, type Transport } from "./http";
import {
  WordPressProvider,
  type WordPressCredentials,
} from "../publishing/wordpress";
import type { ConnectionProvider, Prisma } from "@prisma/client";
export const connectionSelect = {
  id: true,
  siteId: true,
  provider: true,
  status: true,
  label: true,
  accountLabel: true,
  verifiedAt: true,
  lastSyncedAt: true,
  error: true,
  selectedProperty: true,
  tokenExpiresAt: true,
  sync: true,
} as const;
export function event(
  ctx: Pick<Context, "organisationId" | "user">,
  action: string,
  entityId: string,
  detail?: Prisma.InputJsonObject,
) {
  return {
    organisationId: ctx.organisationId,
    actorId: ctx.user.id,
    action,
    entityId,
    ...(detail ? { detail } : {}),
  };
}
export function connectionPermission(ctx: Context) {
  if (!["OWNER", "ADMIN", "SOFTKOM_MANAGER"].includes(ctx.role))
    throw new AccessError(
      403,
      "An owner, administrator or Softkom manager must manage connections.",
    );
}
export async function connectWordPress(
  ctx: Context,
  raw: unknown,
  transport?: Transport,
) {
  connectionPermission(ctx);
  const input = z
    .object({
      siteId: idSchema,
      siteUrl: text(2048),
      username: text(200),
      password: text(300),
      label: text(200),
    })
    .parse(raw);
  const site = await scopedSite(ctx, input.siteId);
  if (site.cms !== "WORDPRESS")
    throw new AccessError(409, "This site does not use WordPress.");
  const url = destination(input.siteUrl);
  if (url.search)
    throw new AccessError(
      409,
      "Use the WordPress installation URL without a query string.",
    );
  const credentials: WordPressCredentials = {
    siteUrl: url.href.replace(/\/$/, ""),
    username: input.username,
    password: input.password,
  };
  // Check encryption configuration before contacting an external account.
  const envelope = encryptCredential(JSON.stringify(credentials));
  const connection = await db.$transaction(async (tx) => {
    const c = await tx.siteConnection.upsert({
      where: { siteId_provider: { siteId: site.id, provider: "WORDPRESS" } },
      create: {
        siteId: site.id,
        provider: "WORDPRESS",
        status: "CONNECTING",
        generation: 1,
      },
      update: {
        status: "CONNECTING",
        generation: { increment: 1 },
        encryptedCredentials: null,
        verifiedAt: null,
        error: null,
      },
    });
    await tx.auditEvent.create({
      data: event(ctx, "connection.created", c.id),
    });
    return c;
  });
  try {
    const verified = await new WordPressProvider(
      credentials,
      transport,
    ).verify();
    await db.$transaction(async (tx) => {
      const result = await tx.siteConnection.updateMany({
        where: {
          id: connection.id,
          generation: connection.generation,
          status: "CONNECTING",
        },
        data: {
          encryptedCredentials: envelope,
          status: "CONNECTED",
          label: input.label,
          accountLabel: verified.accountLabel,
          verifiedAt: new Date(),
          error: null,
        },
      });
      if (!result.count)
        throw new AccessError(
          409,
          "Connection changed while verification was running. Reload and try again.",
        );
      await tx.site.update({
        where: { id: site.id },
        data: { status: "CONNECTED" },
      });
      await tx.auditEvent.create({
        data: event(ctx, "connection.tested", connection.id),
      });
    });
  } catch (error) {
    const safe = providerError(error);
    await db.$transaction(async (tx) => {
      const changed = await tx.siteConnection.updateMany({
        where: {
          id: connection.id,
          generation: connection.generation,
          status: "CONNECTING",
        },
        data: {
          status: "ERROR",
          error: safe.message,
          encryptedCredentials: null,
        },
      });
      if (changed.count) {
        await tx.site.update({
          where: { id: site.id },
          data: { status: "ERROR" },
        });
        await tx.auditEvent.create({
          data: event(ctx, "connection.failed", connection.id, {
            code: safe.code,
          }),
        });
      }
    });
    throw new AccessError(409, safe.message);
  }
}
export async function testWordPress(
  ctx: Context,
  siteId: string,
  transport?: Transport,
) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  const c = await db.siteConnection.findUnique({
    where: { siteId_provider: { siteId, provider: "WORDPRESS" } },
  });
  if (!c?.encryptedCredentials)
    throw new AccessError(409, "Connect WordPress first.");
  try {
    const result = await new WordPressProvider(
      JSON.parse(decryptCredential(c.encryptedCredentials)),
      transport,
    ).verify();
    await db.$transaction(async (tx) => {
      const updated = await tx.siteConnection.updateMany({
        where: {
          id: c.id,
          generation: c.generation,
          encryptedCredentials: c.encryptedCredentials,
        },
        data: {
          verifiedAt: new Date(),
          accountLabel: result.accountLabel,
          status: "CONNECTED",
          error: null,
        },
      });
      if (updated.count) {
        await tx.site.update({
          where: { id: siteId },
          data: { status: "CONNECTED" },
        });
        await tx.auditEvent.create({
          data: event(ctx, "connection.tested", c.id),
        });
      }
    });
  } catch (error) {
    const safe = providerError(error);
    await db.$transaction(async (tx) => {
      const updated = await tx.siteConnection.updateMany({
        where: { id: c.id, generation: c.generation },
        data: { status: "ERROR", error: safe.message },
      });
      if (updated.count) {
        await tx.site.update({
          where: { id: siteId },
          data: { status: "ERROR" },
        });
        await tx.auditEvent.create({
          data: event(ctx, "connection.failed", c.id, { code: safe.code }),
        });
      }
    });
    throw new AccessError(409, safe.message);
  }
}
export async function disconnect(
  ctx: Context,
  siteId: string,
  provider: ConnectionProvider,
) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  await db.$transaction(async (tx) => {
    const c = await tx.siteConnection.findUnique({
      where: { siteId_provider: { siteId, provider } },
    });
    if (!c) throw new AccessError(404, "Connection not found.");
    await tx.siteConnection.update({
      where: { id: c.id },
      data: {
        status: "NOT_CONNECTED",
        encryptedCredentials: null,
        properties: [],
        selectedProperty: null,
        tokenExpiresAt: null,
        verifiedAt: null,
        accountLabel: null,
        error: null,
        generation: { increment: 1 },
      },
    });
    await tx.searchSync.deleteMany({ where: { connectionId: c.id } });
    await tx.oAuthState.deleteMany({
      where: { siteId, organisationId: ctx.organisationId },
    });
    if (provider === "WORDPRESS")
      await tx.site.update({
        where: { id: siteId },
        data: { status: "DISCONNECTED" },
      });
    await tx.auditEvent.create({
      data: event(ctx, "connection.disconnected", c.id),
    });
  });
}
export async function saveBrandTerms(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const terms = z.string().trim().max(3000).parse(raw);
  await db.$transaction(async (tx) => {
    await tx.organisation.update({
      where: { id: ctx.organisationId },
      data: { brandedTerms: terms },
    });
    await tx.auditEvent.create({
      data: event(ctx, "search.brand_terms", ctx.organisationId),
    });
  });
}
