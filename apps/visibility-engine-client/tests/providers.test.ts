import { wordpressFixture } from "./fixtures/providers";
import { describe, it, expect, vi } from "vitest";
import https from "node:https";
import { EventEmitter } from "node:events";
import {
  publicAddress,
  destination,
  checkedDestination,
  safeTransport,
  ProviderError,
  providerError,
  json,
} from "../lib/connections/http";
import { WordPressProvider } from "../lib/publishing/wordpress";
import { GoogleSearchProvider } from "../lib/analytics/google";
import { brandClass, canonicalPage } from "../lib/analytics/reporting";
import { grain, syncWindow } from "../lib/analytics/sync";
import { configuredAIProvider, extractMistral } from "../lib/ai/monitor";
describe("destination and credential boundaries", () => {
  it("rejects private, metadata, transition and encoded destinations", async () => {
    for (const ip of [
      "127.0.0.1",
      "0.0.0.0",
      "10.1.1.1",
      "172.31.0.1",
      "192.168.0.1",
      "169.254.169.254",
      "100.100.100.200",
      "192.0.2.1",
      "224.0.0.1",
      "::1",
      "::",
      "fc00::1",
      "fe80::1",
      "::ffff:127.0.0.1",
      "64:ff9b::a00:1",
      "2002:7f00:1::1",
    ])
      expect(publicAddress(ip), ip).toBe(false);
    for (const url of [
      "https://localhost",
      "https://app.localhost",
      "https://metadata.google.internal",
      "http://example.com",
      "file:///etc/passwd",
      "https://u:p@example.com",
      "https://2130706433",
      "https://0x7f000001",
      "https://[::1]",
    ])
      expect(() => destination(url), url).toThrow();
    expect(publicAddress("8.8.8.8")).toBe(true);
    expect(publicAddress("2606:4700:4700::1111")).toBe(true);
    await expect(
      checkedDestination("https://public.example", async () => [
        { address: "8.8.8.8", family: 4 },
        { address: "10.1.1.1", family: 4 },
      ]),
    ).rejects.toThrow("blocked");
    await expect(
      checkedDestination("https://public.example", async () => []),
    ).rejects.toThrow();
  });
  it("validates redirects and pins the checked DNS address", async () => {
    const requests: unknown[] = [];
    const spy = vi.spyOn(https, "request").mockImplementation(((
      url: URL,
      options: Record<string, unknown>,
      callback: (response: EventEmitter) => void,
    ) => {
      requests.push(url.href);
      const lookup = options.lookup as (
        host: string,
        opts: object,
        cb: (error: unknown, address: string, family: number) => void,
      ) => void;
      lookup(url.hostname, {}, (_error, address) =>
        expect(address).toBe("8.8.8.8"),
      );
      const request = new EventEmitter() as EventEmitter & {
        end: () => void;
        destroy: () => void;
      };
      request.destroy = () => {};
      request.end = () => {
        const response = Object.assign(new EventEmitter(), {
          statusCode: 302,
          headers: { location: "https://127.0.0.1/private" },
        });
        callback(response);
        queueMicrotask(() => response.emit("end"));
      };
      return request;
    }) as typeof https.request);
    try {
      await expect(
        safeTransport(async () => [{ address: "8.8.8.8", family: 4 }])(
          "https://public.example",
        ),
      ).rejects.toThrow();
      expect(requests).toHaveLength(1);
    } finally {
      spy.mockRestore();
    }
  });
  it("sanitises provider errors without retaining provider output", () => {
    const secret = "never-expose-this-token";
    expect(providerError(new Error(secret)).message).not.toContain(secret);
    for (const status of [400, 401, 403, 429, 500])
      expect(() =>
        json({ status, body: secret, headers: {}, url: "https://example.com" }),
      ).toThrow();
    expect(
      providerError(new ProviderError("quota", "Rate limited", true)).retryable,
    ).toBe(true);
  });
});
describe("transparent measurements", () => {
  it("classifies whole brand phrases and retains meaningful URL parameters", () => {
    expect(
      brandClass("SOFTKOM solutions pricing", "Softkom Solutions\nRevenue OS"),
    ).toBe("Branded");
    expect(brandClass("softkomsolutions alternatives", "Softkom")).toBe(
      "Non-branded",
    );
    expect(brandClass("another query", "")).toBe("Unclassified");
    expect(canonicalPage("https://example.com/a/?utm_source=x#x")).toBe(
      "https://example.com/a",
    );
    expect(canonicalPage("https://example.com/?page_id=2")).not.toBe(
      canonicalPage("https://example.com/?page_id=3"),
    );
    expect(
      grain("a", new Date("2026-10-20"), "q", "https://example.com"),
    ).not.toBe(grain("b", new Date("2026-10-20"), "q", "https://example.com"));
    expect(syncWindow(new Date("2026-11-03T10:00:00Z")).to.toISOString()).toBe(
      "2026-10-31T00:00:00.000Z",
    );
  });
});
describe("Google Analytics provider", () => {
  it("discovers properties and parses observed GA4 measurements", async () => {
    const requests: { url: string; body?: string }[] = [];
    const transport = async (
      url: string,
      options: { body?: string } = {},
    ) => {
      requests.push({ url, body: options.body });
      if (url.includes("accountSummaries"))
        return {
          status: 200,
          body: JSON.stringify({
            accountSummaries: [
              {
                displayName: "Softkom",
                propertySummaries: [
                  {
                    property: "properties/123456",
                    displayName: "Softkom Solutions",
                  },
                ],
              },
            ],
          }),
          headers: {},
          url,
        };
      return {
        status: 200,
        body: JSON.stringify({
          metadata: { currencyCode: "ZAR", timeZone: "Africa/Johannesburg" },
          rows: [
            {
              dimensionValues: [
                { value: "20261101" },
                { value: "/services?utm_source=google" },
                { value: "google" },
                { value: "organic" },
              ],
              metricValues: [
                { value: "12" },
                { value: "9" },
                { value: "2" },
                { value: "1500.50" },
              ],
            },
          ],
        }),
        headers: {},
        url,
      };
    };
    const provider = new GoogleSearchProvider(transport);
    await expect(provider.analyticsProperties("token")).resolves.toEqual([
      {
        property: "properties/123456",
        displayName: "Softkom Solutions",
        account: "Softkom",
      },
    ]);
    await expect(
      provider.fetchAnalytics(
        "token",
        "properties/123456",
        new Date("2026-11-01T00:00:00Z"),
        new Date("2026-11-02T00:00:00Z"),
      ),
    ).resolves.toEqual([
      {
        date: new Date("2026-11-01T00:00:00Z"),
        landingPage: "/services?utm_source=google",
        source: "google",
        medium: "organic",
        sessions: 12,
        engagedSessions: 9,
        conversions: 2,
        totalRevenue: 1500.5,
        currency: "ZAR",
      },
    ]);
    expect(JSON.parse(requests[1].body || "{}").metrics).toEqual([
      { name: "sessions" },
      { name: "engagedSessions" },
      { name: "keyEvents" },
      { name: "totalRevenue" },
    ]);
  });

  it("rejects invalid GA4 measurements instead of storing them", async () => {
    const provider = new GoogleSearchProvider(async (url) => ({
      status: 200,
      body: JSON.stringify({
        metadata: { currencyCode: "ZAR", timeZone: "Africa/Johannesburg" },
        rows: [
          {
            dimensionValues: [
              { value: "20261101" },
              { value: "/" },
              { value: "google" },
              { value: "organic" },
            ],
            metricValues: [
              { value: "-1" },
              { value: "0" },
              { value: "0" },
              { value: "0" },
            ],
          },
        ],
      }),
      headers: {},
      url,
    }));
    await expect(
      provider.fetchAnalytics(
        "token",
        "properties/123456",
        new Date("2026-11-01T00:00:00Z"),
        new Date("2026-11-02T00:00:00Z"),
      ),
    ).rejects.toThrow("invalid measurements");
  });
});

describe("WordPress provider", () => {
  it("reconciles a lost create response and reuses the same remote page", async () => {
    const fixture = wordpressFixture();
    const provider = new WordPressProvider(
      {
        siteUrl: "https://wordpress.example",
        username: "fixture",
        password: "fixture-only",
      },
      fixture.transport,
    );
    const input = {
      title: "Approved title",
      html: "<p>Approved body</p>",
      slug: "approved",
      metaTitle: "SEO",
      metaDescription: "Description",
      funnel: "https://wordpress.example/contact",
      idempotencyKey: "publication-1",
    };
    fixture.loseResponse = true;
    await expect(provider.publish(input)).rejects.toThrow();
    const result = await provider.publish(input);
    await provider.verifyPublication(result, input);
    expect(fixture.pages.get(Number(result.externalId))?.title).toBe(
      input.title,
    );
    expect(fixture.pages.get(Number(result.externalId))?.content.raw).toBe(
      `${input.html}\n<!-- ${result.response.marker} -->`,
    );
    await provider.publish({ ...input, externalId: result.externalId });
    expect(fixture.creates).toBe(1);
    expect(JSON.stringify(result)).not.toContain("fixture-only");
  });
});


describe("AI visibility providers", () => {
  it("defaults to Mistral and keeps OpenAI optional", () => {
    expect(
      configuredAIProvider({
        MISTRAL_API_KEY: "fixture-mistral",
      } as NodeJS.ProcessEnv),
    ).toMatchObject({
      provider: "mistral",
      apiKey: "fixture-mistral",
      model: "mistral-small-2603",
      platform: "Mistral web search",
    });

    expect(
      configuredAIProvider({
        AI_VISIBILITY_PROVIDER: "openai",
        OPENAI_API_KEY: "fixture-openai",
        AI_VISIBILITY_MODEL: "gpt-fixture",
      } as NodeJS.ProcessEnv),
    ).toMatchObject({
      provider: "openai",
      apiKey: "fixture-openai",
      model: "gpt-fixture",
      platform: "OpenAI web search",
    });
  });

  it("extracts Mistral answer text and cited URLs without inventing evidence", () => {
    expect(
      extractMistral({
        outputs: [
          {
            content: [
              {
                type: "text",
                text: "Softkom Solutions is one provider.\\nCOMPETITORS: Example Co",
              },
              {
                type: "tool_reference",
                url: "https://softkomsolutions.com/services",
                title: "Softkom",
              },
            ],
          },
        ],
      }),
    ).toEqual({
      text: "Softkom Solutions is one provider.\\nCOMPETITORS: Example Co",
      citations: ["https://softkomsolutions.com/services"],
    });
  });
});
