import { db } from "../db";

type Observation = {
  brandMentioned: boolean;
  pageCited: boolean;
  citationUrl: string | null;
  competitorMentioned: string | null;
};

type OpenAIResponsePayload = {
  output?: Array<{
    content?: Array<{
      text?: string;
      annotations?: Array<{ url?: string }>;
    }>;
  }>;
};

type MistralChunk = {
  type?: string;
  text?: string;
  url?: string;
  title?: string;
  content?: string | MistralChunk[];
};

type MistralConversationPayload = {
  outputs?: Array<{
    type?: string;
    content?: string | MistralChunk[];
  }>;
};

type AIProvider = "mistral" | "openai";

const clean = (value: string, max = 160) =>
  value.replace(/\s+/g, " ").trim().slice(0, max);

const host = (value: string) => {
  try {
    return new URL(value).hostname.replace(/^www\./, "").toLowerCase();
  } catch {
    return "";
  }
};

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

function instruction(prompt: string) {
  return [
    "Answer the buyer question objectively using web search.",
    "Do not favour the named company.",
    "Use citations where sources support the answer.",
    "Finish with one line: COMPETITORS: names separated by semicolons, or None.",
    `Buyer question: ${prompt}`,
  ].join("\n");
}

function classify(
  text: string,
  citations: string[],
  organisationName: string,
  siteUrl: string,
): Observation {
  const targetHost = host(siteUrl);
  const lower = text.toLowerCase();
  const brandTerms = [
    organisationName.toLowerCase(),
    targetHost,
    targetHost.split(".")[0],
  ].filter(Boolean);
  const citationUrl =
    citations.find((url) => host(url) === targetHost) || null;

  return {
    brandMentioned: brandTerms.some((term) => lower.includes(term)),
    pageCited: !!citationUrl,
    citationUrl,
    competitorMentioned: competitorFrom(text),
  };
}

function extractOpenAI(payload: OpenAIResponsePayload) {
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

export function extractMistral(payload: MistralConversationPayload) {
  const text: string[] = [];
  const citations: string[] = [];

  const visit = (value: string | MistralChunk[] | undefined) => {
    if (!value) return;
    if (typeof value === "string") {
      text.push(value);
      return;
    }
    for (const chunk of value) {
      if (chunk.text) text.push(chunk.text);
      if (chunk.url) citations.push(chunk.url);
      if (chunk.content) visit(chunk.content);
    }
  };

  for (const output of payload.outputs || []) visit(output.content);
  return {
    text: text.join("\n"),
    citations: Array.from(new Set(citations)),
  };
}

async function observeOpenAI(
  apiKey: string,
  model: string,
  prompt: string,
  organisationName: string,
  siteUrl: string,
) {
  const response = await fetch("https://api.openai.com/v1/responses", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      model,
      tools: [{ type: "web_search" }],
      input: instruction(prompt),
      max_output_tokens: 1200,
    }),
  });

  if (!response.ok) throw new Error(`AI provider HTTP ${response.status}`);
  const payload = (await response.json()) as OpenAIResponsePayload;
  const evidence = extractOpenAI(payload);
  return classify(evidence.text, evidence.citations, organisationName, siteUrl);
}

async function observeMistral(
  apiKey: string,
  model: string,
  prompt: string,
  organisationName: string,
  siteUrl: string,
) {
  const response = await fetch("https://api.mistral.ai/v1/conversations", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      model,
      inputs: [{ role: "user", content: instruction(prompt) }],
      tools: [{ type: "web_search" }],
      store: false,
    }),
  });

  if (!response.ok) throw new Error(`AI provider HTTP ${response.status}`);
  const payload = (await response.json()) as MistralConversationPayload;
  const evidence = extractMistral(payload);
  if (!evidence.text.trim()) throw new Error("AI provider returned no answer");
  return classify(evidence.text, evidence.citations, organisationName, siteUrl);
}

export function configuredAIProvider(env = process.env): {
  provider: AIProvider;
  apiKey: string | undefined;
  model: string;
  platform: string;
  source: string;
} {
  const requested = (env.AI_VISIBILITY_PROVIDER || "mistral").toLowerCase();
  if (requested === "openai") {
    const model = env.AI_VISIBILITY_MODEL || "gpt-5";
    return {
      provider: "openai",
      apiKey: env.OPENAI_API_KEY,
      model,
      platform: "OpenAI web search",
      source: `OpenAI Responses API / ${model} / web_search`,
    };
  }

  const model =
    env.MISTRAL_VISIBILITY_MODEL ||
    env.AI_VISIBILITY_MODEL ||
    "mistral-small-2603";
  return {
    provider: "mistral",
    apiKey: env.MISTRAL_API_KEY,
    model,
    platform: "Mistral web search",
    source: `Mistral Conversations API / ${model} / web_search`,
  };
}

export async function monitorAIVisibility() {
  const config = configuredAIProvider();
  if (!config.apiKey)
    return {
      created: 0,
      skipped: true,
      errors: 0,
      provider: config.provider,
    };

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
        where: {
          siteId: site.id,
          prompt,
          platform: config.platform,
          observedAt: { gte: since },
        },
      });
      if (existing) continue;

      try {
        const result =
          config.provider === "mistral"
            ? await observeMistral(
                config.apiKey,
                config.model,
                prompt,
                site.organisation.name,
                site.baseUrl,
              )
            : await observeOpenAI(
                config.apiKey,
                config.model,
                prompt,
                site.organisation.name,
                site.baseUrl,
              );

        await db.aIObservation.create({
          data: {
            siteId: site.id,
            platform: config.platform,
            prompt,
            ...result,
            observedAt: new Date(),
            confidence: "Single observed response",
            source: config.source,
          },
        });
        created++;
      } catch {
        errors++;
      }
    }
  }

  return {
    created,
    skipped: false,
    errors,
    provider: config.provider,
  };
}
