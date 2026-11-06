import {
  Prisma,
  SearchIntent,
  TopicStatus,
  CmsType,
  LeadStage,
} from "@prisma/client";
import { z } from "zod";
import { db } from "./db";
import { scopedContent, scopedSite } from "./scope";
import type { Context } from "./context";
import {
  AccessError,
  assertApproved,
  cleanContent,
  idSchema,
  requireManager,
  text,
  webUrl,
} from "./security";

const optional = z.string().trim().max(2000).default("");
const slug = text(180).regex(
  /^[a-z0-9]+(?:-[a-z0-9]+)*$/,
  "Use lowercase words separated by hyphens.",
);
const siteInput = z.object({
  name: text(),
  baseUrl: webUrl,
  cms: z.nativeEnum(CmsType),
});
const topicInput = z.object({
  siteId: idSchema,
  title: text(),
  slug,
  cluster: text(),
  intent: z.nativeEnum(SearchIntent),
  priority: z.coerce.number().int().min(0).max(100),
  funnel: text(),
  reasoning: text(6000),
  businessProblem: text(2000),
  contentType: text(),
  evidence: optional,
});
const contentInput = z.object({
  siteId: idSchema,
  topicId: idSchema,
  title: text(),
  slug,
  body: text(100000),
  metaTitle: text(100),
  metaDesc: text(300),
  contentType: text(),
  funnel: text(),
  internalLinks: optional,
  reviewerId: z.string().max(128).default(""),
  scheduledAt: z.string().max(40).default(""),
});
function audit(
  ctx: Context,
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
const serial = {
  isolationLevel: Prisma.TransactionIsolationLevel.Serializable,
} as const;

export async function addSite(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = siteInput.parse(raw);
  return db.$transaction(async (tx) => {
    const site = await tx.site.create({
      data: {
        ...input,
        organisationId: ctx.organisationId,
        connections: {
          create: [
            { provider: "WORDPRESS", status: "REQUIRES_SETUP" },
            { provider: "GOOGLE_SEARCH_CONSOLE", status: "REQUIRES_SETUP" },
            { provider: "GOOGLE_ANALYTICS_4", status: "COMING_SOON" },
          ],
        },
      },
    });
    await tx.auditEvent.create({ data: audit(ctx, "site.created", site.id) });
    return site;
  });
}
export async function createTopic(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = topicInput.parse(raw);
  await scopedSite(ctx, input.siteId);
  return db.$transaction(async (tx) => {
    const topic = await tx.topic.create({ data: input });
    await tx.auditEvent.create({
      data: audit(ctx, "opportunity.created", topic.id),
    });
    return topic;
  });
}
export async function setTopicStatus(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = z
    .object({ id: idSchema, status: z.nativeEnum(TopicStatus) })
    .parse(raw);
  if (input.status === "PUBLISHED" || input.status === "APPROVED")
    throw new AccessError(
      409,
      "Approval and publication are recorded through the content workflow.",
    );
  return db.$transaction(async (tx) => {
    const result = await tx.topic.updateMany({
      where: { id: input.id, site: { organisationId: ctx.organisationId } },
      data: { status: input.status },
    });
    if (!result.count)
      throw new AccessError(404, "Opportunity not found in this workspace.");
    await tx.auditEvent.create({
      data: audit(ctx, "opportunity.status", input.id, {
        status: input.status,
      }),
    });
  });
}
export async function createContent(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = contentInput.parse(raw);
  await scopedSite(ctx, input.siteId);
  const topic = await db.topic.findFirst({
    where: {
      id: input.topicId,
      siteId: input.siteId,
      site: { organisationId: ctx.organisationId },
    },
  });
  if (!topic) throw new AccessError(404, "Topic not found for this site.");
  if (
    input.reviewerId &&
    !(await db.membership.findFirst({
      where: {
        userId: input.reviewerId,
        organisationId: ctx.organisationId,
        role: { in: ["OWNER", "ADMIN", "CLIENT"] },
      },
    }))
  )
    throw new AccessError(404, "Reviewer not found in this workspace.");
  const scheduledAt = input.scheduledAt
    ? z.coerce.date().parse(
        `${z
          .string()
          .regex(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/)
          .parse(input.scheduledAt)}+02:00`,
      )
    : null;
  const body = cleanContent(input.body);
  if (!body.trim())
    throw new AccessError(409, "Content is empty after sanitisation.");
  return db.$transaction(async (tx) => {
    const content = await tx.contentItem.create({
      data: {
        ...input,
        body,
        scheduledAt,
        reviewerId: input.reviewerId || null,
        status: "CLIENT_REVIEW",
        revisions: {
          create: {
            number: 1,
            slug: input.slug,
            funnel: input.funnel,
            title: input.title,
            body,
            metaTitle: input.metaTitle,
            metaDesc: input.metaDesc,
            internalLinks: input.internalLinks,
            authorId: ctx.user.id,
          },
        },
      },
    });
    await tx.topic.updateMany({
      where: { id: topic.id, site: { organisationId: ctx.organisationId } },
      data: { status: "REVIEW" },
    });
    await tx.auditEvent.create({
      data: audit(ctx, "content.created", content.id, { revision: 1 }),
    });
    return content;
  });
}
export async function reviseContent(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = z
    .object({
      id: idSchema,
      revision: z.coerce.number().int().positive(),
      title: text(),
      body: text(100000),
      metaTitle: text(100),
      metaDesc: text(300),
      internalLinks: optional,
    })
    .parse(raw);
  const body = cleanContent(input.body);
  if (!body.trim())
    throw new AccessError(409, "Content is empty after sanitisation.");
  return db.$transaction(async (tx) => {
    const content = await tx.contentItem.findFirst({
      where: {
        id: input.id,
        revision: input.revision,
        site: { organisationId: ctx.organisationId },
      },
    });
    if (!content)
      throw new AccessError(
        409,
        "Content is unavailable or has changed. Reload before editing.",
      );
    const number = content.revision + 1;
    const { id } = input;
    const snapshot = {
      title: input.title,
      metaTitle: input.metaTitle,
      metaDesc: input.metaDesc,
      internalLinks: input.internalLinks,
    };
    await tx.contentItem.updateMany({
      where: {
        id,
        revision: input.revision,
        site: { organisationId: ctx.organisationId },
      },
      data: { ...snapshot, body, revision: number, status: "CLIENT_REVIEW" },
    });
    await tx.contentRevision.create({
      data: {
        contentId: id,
        number,
        ...snapshot,
        slug: content.slug,
        funnel: content.funnel,
        body,
        authorId: ctx.user.id,
      },
    });
    if (content.topicId)
      await tx.topic.updateMany({
        where: {
          id: content.topicId,
          site: { organisationId: ctx.organisationId },
        },
        data: { status: "REVIEW" },
      });
    await tx.auditEvent.create({
      data: audit(ctx, "content.revised", id, { revision: number }),
    });
  }, serial);
}
export async function reviewContent(ctx: Context, raw: unknown) {
  const input = z
    .object({
      id: idSchema,
      revision: z.coerce.number().int().positive(),
      decision: z.enum(["APPROVED", "CHANGES_REQUESTED", "COMMENT"]),
      comment: z.string().trim().max(6000),
    })
    .parse(raw);
  if (input.decision !== "APPROVED" && !input.comment)
    throw new AccessError(409, "Please include a comment.");
  return db.$transaction(async (tx) => {
    const content = await tx.contentItem.findFirst({
      where: { id: input.id, site: { organisationId: ctx.organisationId } },
    });
    if (!content)
      throw new AccessError(404, "Content not found in this workspace.");
    if (content.revision !== input.revision)
      throw new AccessError(
        409,
        "A new revision is available. Reload before reviewing.",
      );
    if (input.decision === "APPROVED" && ctx.role === "SOFTKOM_MANAGER")
      throw new AccessError(
        403,
        "Client approval is required. Use the audited manager override for an exception.",
      );
    if (
      input.decision !== "COMMENT" &&
      content.reviewerId &&
      content.reviewerId !== ctx.user.id
    )
      throw new AccessError(
        403,
        "Only the assigned reviewer may approve or request changes. All members may comment.",
      );
    if (input.decision !== "COMMENT") {
      if (
        !["CLIENT_REVIEW", "CHANGES_REQUESTED", "APPROVED"].includes(
          content.status,
        )
      )
        throw new AccessError(409, "This content is not open for review.");
      await tx.approval.create({
        data: {
          contentId: content.id,
          reviewerId: ctx.user.id,
          revision: input.revision,
          decision: input.decision,
          comment: input.comment || null,
        },
      });
      await tx.contentItem.updateMany({
        where: {
          id: content.id,
          revision: input.revision,
          site: { organisationId: ctx.organisationId },
        },
        data: { status: input.decision },
      });
      if (content.topicId)
        await tx.topic.updateMany({
          where: {
            id: content.topicId,
            site: { organisationId: ctx.organisationId },
          },
          data: {
            status: input.decision === "APPROVED" ? "APPROVED" : "WRITING",
          },
        });
    } else {
      await tx.contentComment.create({
        data: {
          contentId: content.id,
          authorId: ctx.user.id,
          revision: input.revision,
          body: input.comment,
        },
      });
    }
    await tx.auditEvent.create({
      data: audit(ctx, `review.${input.decision.toLowerCase()}`, content.id, {
        revision: input.revision,
      }),
    });
  }, serial);
}
export async function requestPublication(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = z
    .object({
      id: idSchema,
      overrideReason: z.string().trim().max(2000).optional(),
    })
    .parse(raw);
  await scopedContent(ctx, input.id);
  return db.$transaction(async (tx) => {
    const content = await tx.contentItem.findFirst({
      where: { id: input.id, site: { organisationId: ctx.organisationId } },
      include: { approvals: true, site: true },
    });
    if (!content) throw new AccessError(404, "Content not found.");
    assertApproved(content, ctx.role, input.overrideReason || undefined);
    if (
      !content.metaTitle ||
      !content.metaDesc ||
      !cleanContent(content.body).trim() ||
      !content.funnel
    )
      throw new AccessError(
        409,
        "Add content, SEO metadata and a funnel destination before publishing.",
      );
    const existing = await tx.publication.findUnique({
      where: { jobKey: `${content.id}:${content.revision}` },
    });
    if (existing) return existing;
    const connection = await tx.siteConnection.findUnique({
      where: {
        siteId_provider: { siteId: content.siteId, provider: "WORDPRESS" },
      },
    });
    const available =
      content.site.cms === "WORDPRESS" &&
      connection?.status === "CONNECTED" &&
      !!connection.encryptedCredentials;
    const snapshot = await tx.contentRevision.findUnique({
      where: {
        contentId_number: { contentId: content.id, number: content.revision },
      },
    });
    if (!snapshot)
      throw new AccessError(
        409,
        "Save this revision before requesting publication.",
      );
    const publication = await tx.publication.create({
      data: {
        siteId: content.siteId,
        contentId: content.id,
        provider: content.site.cms,
        requestedRevision: content.revision,
        jobKey: `${content.id}:${content.revision}`,
        requestedBy: ctx.user.id,
        overrideReason: input.overrideReason || null,
        status: available ? "PENDING" : "FAILED",
        attempts: 0,
        error: available
          ? null
          : "WordPress requires a verified connection. No content was sent.",
      },
    });
    await tx.auditEvent.create({
      data: audit(
        ctx,
        input.overrideReason ? "publication.override" : "publication.requested",
        publication.id,
        {
          contentId: content.id,
          revision: content.revision,
          ...(input.overrideReason ? { reason: input.overrideReason } : {}),
        },
      ),
    });
    return publication;
  }, serial);
}
export async function recordLead(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = z
    .object({
      siteId: idSchema,
      contentId: z.string().max(128).default(""),
      landingUrl: webUrl,
      source: optional,
      medium: optional,
      campaign: optional,
      funnel: text(),
      leadId: text(),
      stage: z.nativeEnum(LeadStage),
      revenue: z.string().regex(/^\d{1,9}(\.\d{1,2})?$/),
      currency: z.string().regex(/^[A-Z]{3}$/),
      date: z.coerce.date(),
    })
    .parse(raw);
  await scopedSite(ctx, input.siteId);
  const content = input.contentId
    ? await scopedContent(ctx, input.contentId)
    : null;
  if (content && content.siteId !== input.siteId)
    throw new AccessError(404, "Content does not belong to this site.");
  if (Number(input.revenue) > 0 && input.stage !== "WON")
    throw new AccessError(
      409,
      "Recognised revenue belongs to a won opportunity.",
    );
  const { date, ...data } = input;
  return db.$transaction(async (tx) => {
    const lead = await tx.leadAttribution.create({
      data: {
        ...data,
        contentId: content?.id ?? null,
        cluster: content?.topic?.cluster,
        createdAt: date,
        dataOrigin: "MANUAL",
      },
    });
    await tx.auditEvent.create({
      data: audit(ctx, "attribution.manual", lead.id),
    });
    return lead;
  });
}


export async function recordAIObservation(ctx: Context, raw: unknown) {
  requireManager(ctx.role);
  const input = z
    .object({
      siteId: idSchema,
      platform: text(120),
      prompt: text(12000),
      brandMentioned: z.enum(["yes", "no"]),
      pageCited: z.enum(["yes", "no"]),
      citationUrl: z.string().trim().max(2048).default(""),
      competitorMentioned: optional,
      confidence: text(120),
      source: text(500),
      observedAt: z.coerce.date(),
    })
    .parse(raw);

  const site = await scopedSite(ctx, input.siteId);
  if (input.citationUrl) {
    let parsed: URL;
    try {
      parsed = new URL(input.citationUrl);
    } catch {
      throw new AccessError(409, "Citation URL must be a valid URL.");
    }
    if (!["http:", "https:"].includes(parsed.protocol))
      throw new AccessError(409, "Citation URL must use HTTP or HTTPS.");
    if (parsed.origin !== new URL(site.baseUrl).origin)
      throw new AccessError(
        409,
        "Citation URL must belong to the selected website.",
      );
  }
  if (input.pageCited === "yes" && !input.citationUrl)
    throw new AccessError(
      409,
      "Add the cited page URL when the observation says a page was cited.",
    );

  return db.$transaction(async (tx) => {
    const observation = await tx.aIObservation.create({
      data: {
        siteId: input.siteId,
        platform: input.platform,
        prompt: input.prompt,
        brandMentioned: input.brandMentioned === "yes",
        pageCited: input.pageCited === "yes",
        citationUrl: input.citationUrl || null,
        competitorMentioned: input.competitorMentioned || null,
        observedAt: input.observedAt,
        confidence: input.confidence,
        source: input.source,
      },
    });
    await tx.auditEvent.create({
      data: audit(ctx, "ai_observation.manual", observation.id, {
        siteId: input.siteId,
        platform: input.platform,
      }),
    });
    return observation;
  });
}
