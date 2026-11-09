"use server";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { z } from "zod";
import { randomUUID } from "node:crypto";
import { db } from "@/lib/db";
import { currentUser, getContext } from "@/lib/context";
import { AccessError, canManage, text, webUrl } from "@/lib/security";
import * as workflow from "@/lib/workflow";
import {
  connectWordPress,
  testWordPress,
  disconnect,
  saveBrandTerms,
} from "@/lib/connections/service";
import {
  beginGoogle,
  chooseProperty,
  chooseAnalyticsProperty,
  disconnectGoogle,
} from "@/lib/analytics/service";
import { requestSync } from "@/lib/analytics/sync";
import { requestAnalyticsSync } from "@/lib/analytics/ga4-sync";
import { retryPublication } from "@/lib/publishing/worker";
import { providerError, ProviderError } from "@/lib/connections/http";
import { idSchema } from "@/lib/security";
import { CmsType } from "@prisma/client";
import { PLANS, trialEndsAt } from "@/lib/plans";
import { assertMemberCapacity, invitationHash, invitationToken, requireActivePlan } from "@/lib/saas";
import { OrgRole } from "@prisma/client";

export type ActionState = { error?: string; success?: string };
export async function mutate(
  _previous: ActionState,
  form: FormData,
): Promise<ActionState> {
  const ctx = await getContext();
  try {
    const data = Object.fromEntries(form);
    const operation = String(form.get("operation") || "");
    if (!["disconnect", "review"].includes(operation)) await requireActivePlan(ctx);
    switch (operation) {
      case "wordpressConnect":
        await connectWordPress(ctx, data);
        break;
      case "wordpressTest":
        await testWordPress(ctx, idSchema.parse(data.siteId));
        break;
      case "googleProperty":
        await chooseProperty(
          ctx,
          idSchema.parse(data.siteId),
          z.string().max(2048).parse(data.property),
        );
        break;
      case "searchSync":
        await requestSync(ctx, idSchema.parse(data.siteId));
        break;
      case "analyticsProperty":
        await chooseAnalyticsProperty(
          ctx,
          idSchema.parse(data.siteId),
          z.string().max(128).parse(data.property),
        );
        break;
      case "analyticsSync":
        await requestAnalyticsSync(ctx, idSchema.parse(data.siteId));
        break;
      case "retryPublication":
        await retryPublication(ctx, idSchema.parse(data.id));
        break;
      case "brandTerms":
        await saveBrandTerms(ctx, data.terms);
        break;
      case "disconnect": {
        const siteId = idSchema.parse(data.siteId);
        const provider = z
          .enum(["WORDPRESS", "GOOGLE_SEARCH_CONSOLE", "GOOGLE_ANALYTICS_4"])
          .parse(data.provider);
        if (
          provider === "GOOGLE_SEARCH_CONSOLE" ||
          provider === "GOOGLE_ANALYTICS_4"
        )
          await disconnectGoogle(ctx, siteId);
        else await disconnect(ctx, siteId, provider);
        break;
      }
      case "site":
        await workflow.addSite(ctx, data);
        break;
      case "topic":
        await workflow.createTopic(ctx, data);
        break;
      case "topicStatus":
        await workflow.setTopicStatus(ctx, data);
        break;
      case "content":
        await workflow.createContent(ctx, data);
        break;
      case "revise":
        await workflow.reviseContent(ctx, data);
        break;
      case "review":
        await workflow.reviewContent(ctx, data);
        break;
      case "publish":
        await workflow.requestPublication(ctx, data);
        break;
      case "lead":
        await workflow.recordLead(ctx, data);
        break;
      case "aiObservation":
        await workflow.recordAIObservation(ctx, data);
        break;
      case "invite": {
        await assertMemberCapacity(ctx);
        const input = z.object({
          email: z.string().email().max(254).transform((v) => v.toLowerCase()),
          role: z.nativeEnum(OrgRole),
        }).parse(data);
        if (input.role === "SOFTKOM_MANAGER")
          throw new AccessError(403, "Softkom manager access cannot be invited from a client workspace.");
        const existing = await db.membership.findFirst({
          where: { organisationId: ctx.organisationId, user: { email: input.email } },
        });
        if (existing) throw new AccessError(409, "This person is already a workspace member.");
        const token = invitationToken();
        await db.invitation.create({
          data: {
            organisationId: ctx.organisationId,
            email: input.email,
            role: input.role,
            tokenHash: invitationHash(token),
            invitedBy: ctx.user.id,
            expiresAt: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000),
          },
        });
        await db.auditEvent.create({
          data: {
            organisationId: ctx.organisationId,
            actorId: ctx.user.id,
            action: "membership.invited",
            entityId: input.email,
            detail: { role: input.role },
          },
        });
        return { success: `Invitation created. Share this secure link: /invite/${token}` };
      }
      default:
        throw new AccessError(403, "This action is not available.");
    }
    // Reads are dynamic and uncached. The form refreshes after its action state
    // settles, keeping feedback independent of the route's streamed UI update.
    return {
      success:
        form.get("operation") === "publish"
          ? "Publication request recorded. See its status and retry details below."
          : "Saved to your workspace.",
    };
  } catch (error) {
    if (error instanceof AccessError || error instanceof ProviderError)
      return { error: error.message };
    if (error instanceof z.ZodError)
      return {
        error: error.issues
          .map((i) => `${i.path.join(".")}: ${i.message}`)
          .join(" · "),
      };
    // Never expose Prisma errors, queries or provider credentials to a client.
    return {
      error:
        "This change could not be saved. Check for a duplicate entry or reload and try again.",
    };
  }
}
export async function switchOrganisation(form: FormData) {
  const user = await currentUser();
  const id = z.string().max(128).parse(form.get("organisationId"));
  const membership = await db.membership.findFirst({
    where: { userId: user.id, organisationId: id },
  });
  if (!membership) redirect("/forbidden");
  (await cookies()).set("visibility-org", id, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 86400,
  });
  redirect("/workspace/dashboard");
}
export async function completeOnboarding(
  _previous: ActionState,
  form: FormData,
): Promise<ActionState> {
  const user = await currentUser();
  let organisationId: string;
  try {
    const data = z
      .object({
        businessName: text(),
        name: text(),
        baseUrl: webUrl,
        cms: z.nativeEnum(CmsType),
        services: text(5000),
        customers: text(5000),
        market: text(),
        conversionGoal: text(),
      })
      .parse(Object.fromEntries(form));
    const memberships = await db.membership.findMany({
      where: { userId: user.id },
      orderBy: { id: "asc" },
    });
    const selected = (await cookies()).get("visibility-org")?.value;
    const membership = selected
      ? memberships.find((m) => m.organisationId === selected)
      : memberships[0];
    if (memberships.length && (!membership || !canManage(membership.role)))
      throw new AccessError(
        403,
        "An organisation manager must complete setup.",
      );
    organisationId = await db.$transaction(
      async (tx) => {
        const org = membership
          ? await tx.organisation.update({
              where: { id: membership.organisationId },
              data: { name: data.businessName },
            })
          : await tx.organisation.create({
              data: {
                name: data.businessName,
                slug: `workspace-${randomUUID()}`,
                users: { create: { userId: user.id, role: "OWNER" } },
              },
            });
        await tx.subscription.upsert({
          where: { organisationId: org.id },
          create: {
            organisationId: org.id,
            plan: "TRIAL",
            status: "TRIALING",
            monthlyPrice: 0,
            trialStartedAt: new Date(),
            trialEndsAt: trialEndsAt(),
          },
          update: {},
        });
        await tx.businessProfile.upsert({
          where: { organisationId: org.id },
          create: {
            organisationId: org.id,
            services: data.services,
            customers: data.customers,
            market: data.market,
            conversionGoal: data.conversionGoal,
          },
          update: {
            services: data.services,
            customers: data.customers,
            market: data.market,
            conversionGoal: data.conversionGoal,
            completedAt: new Date(),
          },
        });
        await tx.site.create({
          data: {
            organisationId: org.id,
            name: data.name,
            baseUrl: data.baseUrl,
            cms: data.cms,
            connections: {
              create: [
                { provider: "WORDPRESS", status: "REQUIRES_SETUP" },
                { provider: "GOOGLE_SEARCH_CONSOLE", status: "REQUIRES_SETUP" },
                { provider: "GOOGLE_ANALYTICS_4", status: "COMING_SOON" },
              ],
            },
          },
        });
        await tx.auditEvent.create({
          data: {
            organisationId: org.id,
            actorId: user.id,
            action: "onboarding.completed",
            entityId: org.id,
          },
        });
        return org.id;
      },
      { isolationLevel: "Serializable" },
    );
  } catch (error) {
    return {
      error:
        error instanceof AccessError
          ? error.message
          : error instanceof z.ZodError
            ? error.issues[0].message
            : "Setup could not be saved. Please reload and try again.",
    };
  }
  (await cookies()).set("visibility-org", organisationId, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 86400,
  });
  redirect("/workspace/dashboard");
}

export async function startGoogle(
  _previous: ActionState,
  form: FormData,
): Promise<ActionState> {
  const ctx = await getContext();
  let url: string;
  try {
    url = await beginGoogle(ctx, idSchema.parse(form.get("siteId")));
  } catch (error) {
    return {
      error:
        error instanceof AccessError
          ? error.message
          : providerError(error).message,
    };
  }
  redirect(url);
}


export async function acceptInvitation(form: FormData) {
  const user = await currentUser();
  const token = z.string().min(20).max(200).parse(form.get("token"));
  const hash = invitationHash(token);
  const invite = await db.invitation.findUnique({ where: { tokenHash: hash } });
  if (!invite || invite.acceptedAt || invite.expiresAt <= new Date())
    redirect("/forbidden");
  if (user.email.toLowerCase() !== invite.email.toLowerCase())
    redirect("/forbidden");
  await db.$transaction(async (tx) => {
    await tx.membership.upsert({
      where: {
        organisationId_userId: {
          organisationId: invite.organisationId,
          userId: user.id,
        },
      },
      create: {
        organisationId: invite.organisationId,
        userId: user.id,
        role: invite.role,
      },
      update: { role: invite.role },
    });
    await tx.invitation.update({
      where: { id: invite.id },
      data: { acceptedAt: new Date() },
    });
    await tx.auditEvent.create({
      data: {
        organisationId: invite.organisationId,
        actorId: user.id,
        action: "membership.accepted",
        entityId: user.id,
        detail: { role: invite.role },
      },
    });
  });
  (await cookies()).set("visibility-org", invite.organisationId, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 86400,
  });
  redirect("/workspace/dashboard");
}


export async function activatePlan(form: FormData) {
  const user = await currentUser();
  const allowed = (process.env.SOFTKOM_ADMIN_EMAILS || "")
    .split(",")
    .map((v) => v.trim().toLowerCase())
    .filter(Boolean);
  if (!allowed.includes(user.email.toLowerCase())) redirect("/forbidden");

  const organisationId = idSchema.parse(form.get("organisationId"));
  const plan = z.enum(["TRIAL", "STARTER", "GROWTH", "PRO"]).parse(form.get("plan"));
  const config = PLANS[plan];
  const now = new Date();
  await db.subscription.upsert({
    where: { organisationId },
    create: {
      organisationId,
      plan,
      status: plan === "TRIAL" ? "TRIALING" : "ACTIVE",
      monthlyPrice: config.monthlyPrice,
      trialStartedAt: plan === "TRIAL" ? now : null,
      trialEndsAt: plan === "TRIAL" ? trialEndsAt(now) : null,
      currentPeriodEndsAt:
        plan === "TRIAL" ? null : new Date(now.getTime() + 31 * 24 * 60 * 60 * 1000),
      provider: "MANUAL",
    },
    update: {
      plan,
      status: plan === "TRIAL" ? "TRIALING" : "ACTIVE",
      monthlyPrice: config.monthlyPrice,
      trialStartedAt: plan === "TRIAL" ? now : null,
      trialEndsAt: plan === "TRIAL" ? trialEndsAt(now) : null,
      currentPeriodEndsAt:
        plan === "TRIAL" ? null : new Date(now.getTime() + 31 * 24 * 60 * 60 * 1000),
      provider: "MANUAL",
      cancelAtPeriodEnd: false,
    },
  });
  await db.auditEvent.create({
    data: {
      organisationId,
      actorId: user.id,
      action: "subscription.activated",
      entityId: organisationId,
      detail: { plan, monthlyPrice: config.monthlyPrice },
    },
  });
  redirect("/admin");
}
