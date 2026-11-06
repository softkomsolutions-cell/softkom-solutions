import { createHash } from "node:crypto";
import { db } from "../db";
import { brandClass } from "../analytics/reporting";

type Candidate = {
  title: string;
  slug: string;
  cluster: string;
  intent: "COMMERCIAL" | "DECISION_SUPPORT";
  priority: number;
  funnel: string;
  reasoning: string;
  businessProblem: string;
  contentType: string;
  evidence: string;
};

const clean = (value: string, max = 160) =>
  value.replace(/\s+/g, " ").trim().slice(0, max);

const slugKey = (kind: string, value = "") =>
  `auto-${kind}-${createHash("sha256").update(value || kind).digest("hex").slice(0, 10)}`;

const evidence = (...lines: string[]) =>
  [
    "AUTOMATED EVIDENCE — generated from observed workspace data.",
    ...lines,
    "Review before acting; this is a recommendation, not a guaranteed outcome.",
  ].join("\n");

export async function generateEvidenceOpportunities() {
  const since = new Date(Date.now() - 40 * 86400000);
  const sites = await db.site.findMany({
    include: {
      organisation: { include: { businessProfile: true } },
      metrics: { where: { date: { gte: since } } },
      analyticsMetrics: { where: { date: { gte: since } } },
      aiObservations: { where: { observedAt: { gte: since } } },
    },
  });

  let changed = 0;

  for (const site of sites) {
    const funnel =
      clean(site.organisation.businessProfile?.conversionGoal || "") ||
      "Qualified business enquiry";
    const candidates: Candidate[] = [];
    const impressions = site.metrics.reduce((s, row) => s + row.impressions, 0);
    const nonBrand = site.metrics
      .filter(
        (row) =>
          row.query &&
          brandClass(row.query, site.organisation.brandedTerms) === "Non-branded",
      )
      .reduce((s, row) => s + row.impressions, 0);

    if (impressions > 0 && nonBrand === 0 && site.organisation.brandedTerms.trim()) {
      candidates.push({
        title: "Grow non-branded search discovery",
        slug: slugKey("non-brand"),
        cluster: "Reporting & Visibility",
        intent: "COMMERCIAL",
        priority: 72,
        funnel,
        businessProblem:
          "Observed search demand is concentrated in branded queries, limiting discovery by prospects who do not already know the business.",
        reasoning:
          "Search Console currently shows impressions but no observed non-branded impressions in the evidence window. Improve commercially useful pages around customer problems, services and decision-stage searches.",
        contentType: "Commercial problem page",
        evidence: evidence(
          `Observed search impressions: ${impressions}.`,
          "Observed non-branded impressions: 0.",
        ),
      });
    }

    const queries = new Map<string, { impressions: number; clicks: number; pos: number }>();
    for (const row of site.metrics) {
      if (!row.query) continue;
      const item = queries.get(row.query) || { impressions: 0, clicks: 0, pos: 0 };
      item.impressions += row.impressions;
      item.clicks += row.clicks;
      if (row.position !== null) item.pos += row.position * row.impressions;
      queries.set(row.query, item);
    }

    for (const [query, item] of queries) {
      if (item.impressions < 5) continue;
      const position = item.impressions ? item.pos / item.impressions : 0;
      const ctr = item.impressions ? item.clicks / item.impressions : 0;

      if (position >= 4 && position <= 15) {
        candidates.push({
          title: `Improve visibility for “${clean(query, 80)}”`,
          slug: slugKey("near-page-one", query),
          cluster: "Reporting & Visibility",
          intent: "COMMERCIAL",
          priority: Math.min(88, 58 + Math.round(item.impressions / 5)),
          funnel,
          businessProblem:
            "A relevant query is already visible but is not consistently occupying the strongest result positions.",
          reasoning:
            "Search Console has observed demand and the weighted position is within reach of stronger page-one visibility. Review intent alignment, page usefulness, internal links and snippet quality before creating new content.",
          contentType: "Existing page improvement",
          evidence: evidence(
            `Query: ${query}`,
            `Observed impressions: ${item.impressions}.`,
            `Observed clicks: ${item.clicks}.`,
            `Weighted position: ${position.toFixed(1)}.`,
          ),
        });
      } else if (item.impressions >= 20 && ctr < 0.02) {
        candidates.push({
          title: `Improve click-through for “${clean(query, 80)}”`,
          slug: slugKey("low-ctr", query),
          cluster: "Reporting & Visibility",
          intent: "DECISION_SUPPORT",
          priority: 70,
          funnel,
          businessProblem:
            "The business is appearing for a search query but recorded click-through is weak.",
          reasoning:
            "The query has meaningful observed impressions with a low recorded CTR. Check title, description, page intent and the commercial proposition.",
          contentType: "Search snippet and page improvement",
          evidence: evidence(
            `Query: ${query}`,
            `Observed impressions: ${item.impressions}.`,
            `Observed CTR: ${(ctr * 100).toFixed(2)}%.`,
          ),
        });
      }
    }

    const landing = new Map<string, { sessions: number; conversions: number }>();
    for (const row of site.analyticsMetrics) {
      const item = landing.get(row.landingPage) || { sessions: 0, conversions: 0 };
      item.sessions += row.sessions;
      item.conversions += row.conversions;
      landing.set(row.landingPage, item);
    }
    for (const [page, item] of landing) {
      if (item.sessions < 20 || item.conversions > 0) continue;
      candidates.push({
        title: `Strengthen conversion on ${clean(page || "a high-traffic landing page", 90)}`,
        slug: slugKey("traffic-no-conversion", page),
        cluster: "Revenue Recovery",
        intent: "COMMERCIAL",
        priority: Math.min(92, 70 + Math.round(item.sessions / 20)),
        funnel,
        businessProblem:
          "A landing page is receiving observed sessions without a recorded GA4 key event.",
        reasoning:
          "Traffic without a recorded conversion deserves a conversion-path review before more acquisition is added. Check the offer, CTA prominence, form journey, mobile usability and key-event configuration.",
        contentType: "Conversion improvement",
        evidence: evidence(
          `Landing page: ${page || "(not set)"}`,
          `Observed sessions: ${item.sessions}.`,
          `Observed key events: ${item.conversions.toFixed(1)}.`,
        ),
      });
    }

    const mentionNoCitation = site.aiObservations.filter(
      (row) => row.brandMentioned && !row.pageCited,
    );
    if (mentionNoCitation.length) {
      candidates.push({
        title: "Turn AI brand mentions into attributable citations",
        slug: slugKey("ai-mention-no-citation"),
        cluster: "Practical AI",
        intent: "DECISION_SUPPORT",
        priority: 78,
        funnel,
        businessProblem:
          "Observed AI responses mentioned the brand without citing a page from the website.",
        reasoning:
          "The brand is entering AI answers, but the measured responses did not provide an attributable website citation. Strengthen source-worthy service, proof, case-study and explanatory pages.",
        contentType: "Evidence and authority improvement",
        evidence: evidence(
          `Observed brand mentions without a site citation: ${mentionNoCitation.length}.`,
        ),
      });
    }

    const competitorGap = site.aiObservations.filter(
      (row) => !row.brandMentioned && !!row.competitorMentioned,
    );
    if (competitorGap.length) {
      candidates.push({
        title: "Close observed AI competitor visibility gaps",
        slug: slugKey("ai-competitor-gap"),
        cluster: "Practical AI",
        intent: "COMMERCIAL",
        priority: 82,
        funnel,
        businessProblem:
          "Measured AI responses surfaced competitors while the business was not mentioned.",
        reasoning:
          "This is a prompt-specific visibility gap, not a universal AI ranking. Review which entities were cited and build stronger, verifiable pages that answer the same buyer question.",
        contentType: "Authority and comparison content",
        evidence: evidence(
          `Observed prompts with competitor mentions and no brand mention: ${competitorGap.length}.`,
        ),
      });
    }

    for (const candidate of candidates.slice(0, 8)) {
      const existing = await db.topic.findUnique({
        where: { siteId_slug: { siteId: site.id, slug: candidate.slug } },
      });
      if (existing?.status === "PUBLISHED") continue;

      if (existing) {
        await db.topic.update({
          where: { id: existing.id },
          data: candidate,
        });
      } else {
        await db.topic.create({ data: { siteId: site.id, ...candidate } });
      }
      changed++;
    }
  }

  return changed;
}
