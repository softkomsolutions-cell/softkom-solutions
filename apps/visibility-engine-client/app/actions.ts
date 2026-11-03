"use server";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { z } from "zod";
import { randomUUID } from "node:crypto";
import { db } from "@/lib/db";
import { currentUser, getContext } from "@/lib/context";
import { AccessError, canManage, text, webUrl } from "@/lib/security";
import * as workflow from "@/lib/workflow";
import { CmsType } from "@prisma/client";

export type ActionState = { error?: string; success?: string };
export async function mutate(
  _previous: ActionState,
  form: FormData,
): Promise<ActionState> {
  const ctx = await getContext();
  try {
    const data = Object.fromEntries(form);
    switch (form.get("operation")) {
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
      default:
        throw new AccessError(403, "This action is not available.");
    }
    // Reads are dynamic and uncached. The form refreshes after its action state
    // settles, keeping feedback independent of the route's streamed UI update.
    return {
      success:
        form.get("operation") === "publish"
          ? "Request recorded. The connector is unavailable; nothing was published. See publication history."
          : "Saved to your workspace.",
    };
  } catch (error) {
    if (error instanceof AccessError) return { error: error.message };
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
                { provider: "GOOGLE_SEARCH_CONSOLE", status: "COMING_SOON" },
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
