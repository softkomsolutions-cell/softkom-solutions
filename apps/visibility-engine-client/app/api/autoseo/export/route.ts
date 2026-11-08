import { NextRequest, NextResponse } from "next/server";
import { timingSafeEqual } from "node:crypto";
import { db } from "@/lib/db";

function safeEqual(a: string, b: string) {
  const left = Buffer.from(a);
  const right = Buffer.from(b);
  return left.length === right.length && timingSafeEqual(left, right);
}

function authorised(request: NextRequest) {
  const expected = process.env.AUTOSEO_BRIDGE_SECRET || "";
  const header = request.headers.get("authorization") || "";
  if (!expected || !header.startsWith("Bearer ")) return false;
  return safeEqual(header.slice(7), expected);
}

function day(value: Date) {
  return value.toISOString().slice(0, 10);
}

export async function GET(request: NextRequest) {
  if (!authorised(request)) {
    return NextResponse.json({ error: "unauthorised" }, { status: 401 });
  }

  const siteUrl = request.nextUrl.searchParams.get("site")?.trim();
  if (!siteUrl) {
    return NextResponse.json({ error: "site is required" }, { status: 400 });
  }

  const site = await db.site.findFirst({
    where: { baseUrl: siteUrl },
    include: {
      organisation: { select: { name: true } },
      connections: {
        select: {
          provider: true,
          status: true,
          selectedProperty: true,
          lastSyncedAt: true,
          error: true,
        },
      },
    },
  });

  if (!site) {
    return NextResponse.json({ error: "site not found" }, { status: 404 });
  }

  const to = new Date();
  to.setUTCHours(0, 0, 0, 0);
  to.setUTCDate(to.getUTCDate() - 1);
  const from = new Date(to);
  from.setUTCDate(from.getUTCDate() - 27);

  const [search, analytics, ai, leads] = await Promise.all([
    db.searchMetric.findMany({
      where: {
        siteId: site.id,
        source: "GOOGLE_SEARCH_CONSOLE",
        date: { gte: from, lte: to },
      },
      orderBy: [{ impressions: "desc" }],
      take: 500,
      select: {
        date: true,
        query: true,
        pageUrl: true,
        clicks: true,
        impressions: true,
        ctr: true,
        position: true,
      },
    }),
    db.analyticsMetric.findMany({
      where: {
        siteId: site.id,
        date: { gte: from, lte: to },
      },
      select: {
        date: true,
        landingPage: true,
        source: true,
        medium: true,
        sessions: true,
        engagedSessions: true,
        conversions: true,
        totalRevenue: true,
        currency: true,
      },
    }),
    db.aIObservation.findMany({
      where: {
        siteId: site.id,
        observedAt: { gte: from },
      },
      orderBy: { observedAt: "desc" },
      take: 250,
      select: {
        platform: true,
        prompt: true,
        brandMentioned: true,
        pageCited: true,
        citationUrl: true,
        competitorMentioned: true,
        observedAt: true,
        confidence: true,
        source: true,
      },
    }),
    db.leadAttribution.findMany({
      where: {
        siteId: site.id,
        createdAt: { gte: from, lte: new Date(to.getTime() + 86400000 - 1) },
      },
      select: {
        source: true,
        medium: true,
        campaign: true,
        funnel: true,
        stage: true,
        currency: true,
        revenue: true,
        createdAt: true,
      },
    }),
  ]);

  const channelMap = new Map<string, {
    sessions: number;
    engagedSessions: number;
    keyEvents: number;
    revenue: number;
    currency: string;
  }>();

  for (const row of analytics) {
    const key = [row.source || "(direct)", row.medium || "(none)"].join(" / ");
    const current = channelMap.get(key) || {
      sessions: 0,
      engagedSessions: 0,
      keyEvents: 0,
      revenue: 0,
      currency: row.currency || "ZAR",
    };
    current.sessions += row.sessions;
    current.engagedSessions += row.engagedSessions;
    current.keyEvents += row.conversions;
    current.revenue += Number(row.totalRevenue);
    if (row.currency) current.currency = row.currency;
    channelMap.set(key, current);
  }

  const platforms = new Map<string, {
    runs: number;
    mentions: number;
    citations: number;
    competitors: string[];
  }>();

  for (const row of ai) {
    const current = platforms.get(row.platform) || {
      runs: 0,
      mentions: 0,
      citations: 0,
      competitors: [],
    };
    current.runs += 1;
    if (row.brandMentioned) current.mentions += 1;
    if (row.pageCited) current.citations += 1;
    if (row.competitorMentioned && !current.competitors.includes(row.competitorMentioned)) {
      current.competitors.push(row.competitorMentioned);
    }
    platforms.set(row.platform, current);
  }

  return NextResponse.json({
    site: {
      id: site.id,
      name: site.name,
      baseUrl: site.baseUrl,
      organisation: site.organisation.name,
    },
    window: { from: day(from), to: day(to) },
    connections: site.connections,
    search: search.map((row) => ({
      ...row,
      date: day(row.date),
    })),
    analytics: {
      channels: Array.from(channelMap.entries()).map(([channel, value]) => ({
        channel,
        ...value,
      })),
      rows: analytics.length,
    },
    ai: {
      platforms: Array.from(platforms.entries()).map(([platform, value]) => ({
        platform,
        ...value,
        mentionFrequency: value.runs ? value.mentions / value.runs : 0,
        citationFrequency: value.runs ? value.citations / value.runs : 0,
      })),
      observations: ai.map((row) => ({
        ...row,
        observedAt: row.observedAt.toISOString(),
      })),
    },
    leads: leads.map((row) => ({
      ...row,
      revenue: row.revenue === null ? null : Number(row.revenue),
      createdAt: row.createdAt.toISOString(),
    })),
    generatedAt: new Date().toISOString(),
  });
}
