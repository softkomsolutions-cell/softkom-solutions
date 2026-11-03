import { createHash, randomBytes } from "node:crypto";
import { db } from "../db";
import { scopedSite } from "../scope";
import type { Context } from "../context";
import { AccessError } from "../security";
import {
  encryptCredential,
  decryptCredential,
} from "../connections/credentials";
import {
  connectionPermission,
  disconnect,
  event,
} from "../connections/service";
import {
  providerError,
  ProviderError,
  type Transport,
} from "../connections/http";
import {
  GoogleSearchProvider,
  googleConfig,
  type GoogleTokens,
} from "./google";
const hash = (value: string) =>
  createHash("sha256").update(value).digest("hex");
export async function beginGoogle(ctx: Context, siteId: string) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  const config = googleConfig();
  const state = randomBytes(32).toString("base64url"),
    verifier = randomBytes(32).toString("base64url");
  const envelope = encryptCredential(verifier);
  await db.$transaction(async (tx) => {
    const c = await tx.siteConnection.upsert({
      where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
      create: {
        siteId,
        provider: "GOOGLE_SEARCH_CONSOLE",
        status: "CONNECTING",
        generation: 1,
      },
      update: {
        status: "CONNECTING",
        generation: { increment: 1 },
        error: null,
      },
    });
    await tx.oAuthState.deleteMany({
      where: { siteId, organisationId: ctx.organisationId },
    });
    await tx.oAuthState.create({
      data: {
        hash: hash(state),
        userId: ctx.user.id,
        organisationId: ctx.organisationId,
        siteId,
        verifier: envelope,
        generation: c.generation,
        expiresAt: new Date(Date.now() + 10 * 60 * 1000),
      },
    });
    await tx.auditEvent.create({
      data: event(ctx, "connection.created", c.id),
    });
  });
  const url = new URL("https://accounts.google.com/o/oauth2/v2/auth");
  url.search = new URLSearchParams({
    client_id: config.clientId,
    redirect_uri: config.callback,
    response_type: "code",
    scope: "https://www.googleapis.com/auth/webmasters.readonly",
    access_type: "offline",
    prompt: "consent",
    state,
    code_challenge: createHash("sha256").update(verifier).digest("base64url"),
    code_challenge_method: "S256",
  }).toString();
  return url.href;
}
export async function finishGoogle(
  ctx: Context,
  state: string,
  code: string | null,
  transport?: Transport,
) {
  connectionPermission(ctx);
  if (state.length > 200 || !state)
    throw new AccessError(403, "Google OAuth state is invalid or expired.");
  const saved = await db.oAuthState.findUnique({
    where: { hash: hash(state) },
  });
  if (
    !saved ||
    saved.userId !== ctx.user.id ||
    saved.organisationId !== ctx.organisationId ||
    saved.expiresAt < new Date()
  )
    throw new AccessError(403, "Google OAuth state is invalid or expired.");
  await scopedSite(ctx, saved.siteId);
  const consumed = await db.oAuthState.deleteMany({
    where: { hash: saved.hash },
  });
  if (!consumed.count)
    throw new AccessError(403, "Google OAuth state was already used.");
  const connection = await db.siteConnection.findUniqueOrThrow({
    where: {
      siteId_provider: {
        siteId: saved.siteId,
        provider: "GOOGLE_SEARCH_CONSOLE",
      },
    },
  });
  try {
    if (
      connection.generation !== saved.generation ||
      connection.status !== "CONNECTING"
    )
      throw new ProviderError(
        "oauth",
        "Connection changed. Start Google authorisation again.",
      );
    if (!code || code.length > 4000)
      throw new ProviderError(
        "oauth",
        "Google authorisation was cancelled. Connect again when ready.",
      );
    const provider = new GoogleSearchProvider(transport);
    const tokens = await provider.exchange(
      code,
      decryptCredential(saved.verifier),
    );
    if (!tokens.refresh_token)
      throw new ProviderError(
        "oauth",
        "Google did not grant offline access. Reconnect and grant consent.",
      );
    const properties = await provider.properties(tokens.access_token);
    await db.$transaction(async (tx) => {
      const updated = await tx.siteConnection.updateMany({
        where: {
          id: connection.id,
          generation: saved.generation,
          status: "CONNECTING",
        },
        data: {
          encryptedCredentials: encryptCredential(JSON.stringify(tokens)),
          tokenExpiresAt: new Date(tokens.expires_at!),
          properties,
          selectedProperty: null,
          lastSyncedAt: null,
          status: "REQUIRES_SETUP",
          verifiedAt: new Date(),
          error: null,
        },
      });
      if (!updated.count)
        throw new AccessError(409, "Connection changed during authorisation.");
      await tx.searchSync.deleteMany({
        where: { connectionId: connection.id },
      });
      await tx.auditEvent.create({
        data: event(ctx, "connection.tested", connection.id),
      });
    });
  } catch (error) {
    const safe = providerError(error);
    await db.$transaction(async (tx) => {
      const updated = await tx.siteConnection.updateMany({
        where: { id: connection.id, generation: saved.generation },
        data: { status: "ERROR", error: safe.message },
      });
      if (updated.count)
        await tx.auditEvent.create({
          data: event(ctx, "connection.failed", connection.id, {
            code: safe.code,
          }),
        });
    });
    throw new AccessError(409, safe.message);
  }
}
export async function googleAccess(
  connectionId: string,
  transport?: Transport,
) {
  const c = await db.siteConnection.findUniqueOrThrow({
    where: { id: connectionId },
  });
  if (
    !c.encryptedCredentials ||
    !["CONNECTED", "REQUIRES_SETUP"].includes(c.status)
  )
    throw new ProviderError(
      "oauth",
      "Google Search Console requires reconnection.",
    );
  const tokens = JSON.parse(
    decryptCredential(c.encryptedCredentials),
  ) as GoogleTokens;
  if (tokens.expires_at && tokens.expires_at > Date.now() + 60000)
    return { connection: c, tokens };
  if (!tokens.refresh_token)
    throw new ProviderError(
      "oauth",
      "Google offline access is unavailable. Reconnect Google.",
    );
  try {
    const refreshed = await new GoogleSearchProvider(transport).refresh(
      tokens.refresh_token,
    );
    const next = {
      ...tokens,
      ...refreshed,
      refresh_token: refreshed.refresh_token || tokens.refresh_token,
    };
    const changed = await db.$transaction(async (tx) => {
      const update = await tx.siteConnection.updateMany({
        where: {
          id: c.id,
          generation: c.generation,
          encryptedCredentials: c.encryptedCredentials,
        },
        data: {
          encryptedCredentials: encryptCredential(JSON.stringify(next)),
          tokenExpiresAt: new Date(next.expires_at!),
          error: null,
        },
      });
      if (update.count)
        await tx.auditEvent.create({
          data: {
            organisationId: (
              await tx.site.findUniqueOrThrow({ where: { id: c.siteId } })
            ).organisationId,
            actorId: "search-worker",
            action: "connection.refreshed",
            entityId: c.id,
          },
        });
      return update.count;
    });
    if (!changed)
      throw new ProviderError(
        "changed",
        "Connection changed during token refresh. Retry the sync.",
        true,
      );
    return { connection: c, tokens: next };
  } catch (error) {
    const safe = providerError(error);
    if (!safe.retryable)
      await db.$transaction(async (tx) => {
        const updated = await tx.siteConnection.updateMany({
          where: {
            id: c.id,
            generation: c.generation,
            encryptedCredentials: c.encryptedCredentials,
          },
          data: { status: "ERROR", error: safe.message },
        });
        if (updated.count)
          await tx.auditEvent.create({
            data: {
              organisationId: (
                await tx.site.findUniqueOrThrow({ where: { id: c.siteId } })
              ).organisationId,
              actorId: "search-worker",
              action: "connection.failed",
              entityId: c.id,
              detail: { code: safe.code },
            },
          });
      });
    throw safe;
  }
}
export async function googleProperties(
  ctx: Context,
  siteId: string,
  transport?: Transport,
) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  const c = await db.siteConnection.findUnique({
    where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
  });
  if (!c) throw new AccessError(404, "Google connection not found.");
  const { tokens } = await googleAccess(c.id, transport);
  return new GoogleSearchProvider(transport).properties(tokens.access_token);
}
export async function chooseProperty(
  ctx: Context,
  siteId: string,
  property: string,
  transport?: Transport,
) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  if (property.length > 2048)
    throw new AccessError(409, "Property is too long.");
  const c = await db.siteConnection.findUniqueOrThrow({
    where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
  });
  const { tokens } = await googleAccess(c.id, transport);
  const properties = await new GoogleSearchProvider(transport).properties(
    tokens.access_token,
  );
  if (!properties.some((p) => p.siteUrl === property))
    throw new AccessError(
      403,
      "This Google account cannot access the selected Search Console property.",
    );
  await db.$transaction(async (tx) => {
    const update = await tx.siteConnection.updateMany({
      where: { id: c.id, generation: c.generation },
      data: {
        selectedProperty: property,
        lastSyncedAt: null,
        properties,
        status: "CONNECTED",
        verifiedAt: new Date(),
        error: null,
        generation: { increment: 1 },
      },
    });
    if (!update.count)
      throw new AccessError(
        409,
        "Connection changed. Reload before selecting a property.",
      );
    await tx.searchSync.deleteMany({ where: { connectionId: c.id } });
    await tx.auditEvent.create({
      data: event(ctx, "property.selected", c.id, { property }),
    });
  });
}
export async function disconnectGoogle(
  ctx: Context,
  siteId: string,
  transport?: Transport,
) {
  connectionPermission(ctx);
  await scopedSite(ctx, siteId);
  const c = await db.siteConnection.findUnique({
    where: { siteId_provider: { siteId, provider: "GOOGLE_SEARCH_CONSOLE" } },
  });
  // Local access is removed even if Google's revocation service is unavailable.
  await disconnect(ctx, siteId, "GOOGLE_SEARCH_CONSOLE");
  if (c?.encryptedCredentials)
    try {
      await new GoogleSearchProvider(transport).revoke(
        JSON.parse(decryptCredential(c.encryptedCredentials)),
      );
    } catch {
      await db.auditEvent.create({
        data: event(ctx, "connection.revocation_failed", c.id),
      });
    }
}
