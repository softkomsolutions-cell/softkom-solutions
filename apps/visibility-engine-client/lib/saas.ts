import { createHash, randomBytes } from "node:crypto";
import { db } from "./db";
import type { Context } from "./context";
import { AccessError, canManage } from "./security";
import { PLANS, planKey, subscriptionActive } from "./plans";

export async function subscriptionFor(organisationId: string) {
  return db.subscription.findUnique({ where: { organisationId } });
}

export async function requireActivePlan(ctx: Context) {
  const subscription = await subscriptionFor(ctx.organisationId);
  if (!subscriptionActive(subscription))
    throw new AccessError(403, "Your trial or subscription has ended. Choose a plan to continue making changes.");
  return subscription;
}

export async function assertSiteCapacity(ctx: Context) {
  const subscription = await requireActivePlan(ctx);
  const key = planKey(subscription?.plan);
  const count = await db.site.count({ where: { organisationId: ctx.organisationId } });
  if (count >= PLANS[key].sites)
    throw new AccessError(409, `${PLANS[key].name} supports ${PLANS[key].sites} site${PLANS[key].sites === 1 ? "" : "s"}. Upgrade before adding another site.`);
}

export async function assertMemberCapacity(ctx: Context) {
  if (!canManage(ctx.role)) throw new AccessError(403, "A workspace manager must invite members.");
  const subscription = await requireActivePlan(ctx);
  const key = planKey(subscription?.plan);
  const [members, pendingInvites] = await Promise.all([
    db.membership.count({ where: { organisationId: ctx.organisationId } }),
    db.invitation.count({
      where: {
        organisationId: ctx.organisationId,
        acceptedAt: null,
        expiresAt: { gt: new Date() },
      },
    }),
  ]);
  const count = members + pendingInvites;
  if (count >= PLANS[key].members)
    throw new AccessError(409, `${PLANS[key].name} supports ${PLANS[key].members} members. Upgrade before inviting another person.`);
}

export function invitationToken() {
  return randomBytes(32).toString("base64url");
}
export function invitationHash(token: string) {
  return createHash("sha256").update(token).digest("hex");
}
