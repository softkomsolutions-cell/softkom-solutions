import { db } from "../db";

type ResponsePayload = {
  output?: Array<{
    content?: Array<{
      text?: string;
      annotations?: Array<{ url?: string }>;
    }>;
  }>;
};

const clean = (value: string, max = 160) =>
  value.replace(/\s+/g, " ").trim().slice(0, max);

const host = (value: string) => {
  try {
    return new URL(value).hostname.replace(/^www\./, "").toLowerCase();
  } catch {
    return "";
  }
};

function extract(payload: ResponsePayload) {
  const text: string[] = [];
  const citations: string[] = [];
  for (const item of payload.output || []) {
    for (const part of item.content || []) {
      if (part.text) text.push(part.text);
      for (const annotation of part.annotations || []) {
        if (annotation.url) citations.push(annotation.url);
      }
    }
  }
  return { text: text.join("\n"), citations };
}

function competitorFrom(text: string) {
  const match = text.match(/(?:^|\n)COMPETITORS\s*:\s*([^\n]+)/i);
  if (!match) return null;
  const value = match[1].trim();
  return /^(none|not observed|n\/a)$/i.test(value)
    ? null
    : clean(value, 300);
}

function promptsFor(
  services: string,
  customers: string,
  market: string,
  topics: { title: string }[],
) {
  const prompts = topics.slice(0, 2).map(
    (topic) =>
      `What are reputable providers or practical solutions for ${clean(topic.title, 110)} in ${clean(market, 90)}?`,
  );
  prompts.push(
    `Which companies are credible providers of ${clean(services, 120)} for ${clean(customers, 100)} in ${clean(market, 90)}?`,
  );
  return Array.from(new Set(prompts)).slice(0, 3);
}

async function observe(
  apiKey: string,
  model: string,
  prompt: string,
  organisationName: string,
  siteUrl: string,
) {
  const instruction = [
    "Answer the buyer question objectively using web search.",
    "Do not favour the named company.",
    "Use citations where sources support the answer.",
    "Finish with one line: COMPETITORS: names separated by semicolons, or None.",
    `Buyer question: ${prompt}`,
  ].join("\n");

  const response = await fetch("https://api.openai.com/v1/responses", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      model,
      tools: [{ type: "web_search" }],
      input: instruction,
      max_output_tokens: 1200,
    }),
  });

  if (!response.ok) throw new Error(`AI provider HTTP ${response.status}`);

  const payload = (await response.json()) as ResponsePayload;
  const evidence = extract(payload);
  const targetHost = host(siteUrl);
  const lower = evidence.text.toLowerCase();
  const brandTerms = [
    organisationName.toLowerCase(),
    targetHost,
    targetHost.split(".")[0],
  ].filter(Boolean);
  const citationUrl =
    evidence.citations.find((url) => host(url) === targetHost) || null;

  return {
    brandMentioned: brandTerms.some((term) => lower.includes(term)),
    pageCited: !!citationUrl,
    citationUrl,
    competitorMentioned: competitorFrom(evidence.text),
  };
}

export async function monitorAIVisibility() {
  const apiKey = process.env.OPENAI_API_KEY;
  if (!apiKey) return { created: 0, skipped: true, errors: 0 };

  const model = process.env.AI_VISIBILITY_MODEL || "gpt-5";
  const platform = "OpenAI web search";
  const since = new Date(Date.now() - 23 * 60 * 60 * 1000);
  const sites = await db.site.findMany({
    include: {
      organisation: { include: { businessProfile: true } },
      topics: {
        where: { status: { not: "PAUSED" } },
        orderBy: { priority: "desc" },
        take: 4,
      },
    },
  });

  let created = 0;
  let errors = 0;

  for (const site of sites) {
    const profile = site.organisation.businessProfile;
    if (!profile) continue;

    for (const prompt of promptsFor(
      profile.services,
      profile.customers,
      profile.market,
      site.topics,
    )) {
      const existing = await db.aIObservation.findFirst({
        where: { siteId: site.id, prompt, platform, observedAt: { gte: since } },
      });
      if (existing) continue;

      try {
        const result = await observe(
          apiKey,
          model,
          prompt,
          site.organisation.name,
          site.baseUrl,
        );
        await db.aIObservation.create({
          data: {
            siteId: site.id,
            platform,
            prompt,
            ...result,
            observedAt: new Date(),
            confidence: "Single observed response",
            source: `OpenAI Responses API / ${model} / web_search`,
          },
        });
        created++;
      } catch {
        errors++;
      }
    }
  }

  return { created, skipped: false, errors };
}
