import { http, json, ProviderError, type Transport } from "../connections/http";
import type { SearchObservation } from "./providers";
export type GoogleTokens = {
  access_token: string;
  refresh_token?: string;
  expires_in: number;
  expires_at?: number;
};
export type Property = { siteUrl: string; permissionLevel: string };
export type Ga4Property = { property: string; displayName: string; account: string };
export type Ga4Observation = {
  date: Date;
  landingPage: string;
  source: string;
  medium: string;
  sessions: number;
  engagedSessions: number;
  conversions: number;
  totalRevenue: number;
};
export function googleConfig() {
  const {
    GOOGLE_CLIENT_ID: clientId,
    GOOGLE_CLIENT_SECRET: clientSecret,
    GOOGLE_CALLBACK_URL: callback,
  } = process.env;
  if (!clientId || !clientSecret || !callback)
    throw new ProviderError(
      "setup",
      "Google OAuth requires administrator setup.",
    );
  const url = new URL(callback);
  if (
    url.origin !== new URL(process.env.BETTER_AUTH_URL!).origin ||
    url.pathname !== "/api/connections/google/callback" ||
    (url.protocol !== "https:" &&
      !(
        process.env.NODE_ENV !== "production" &&
        ["localhost", "127.0.0.1"].includes(url.hostname)
      ))
  )
    throw new ProviderError(
      "setup",
      "Google OAuth callback must match this workspace's secure origin.",
    );
  return { clientId, clientSecret, callback };
}
export class GoogleSearchProvider {
  private transport: Transport;
  constructor(transport: Transport = http) {
    const deadline = Date.now() + 240000;
    this.transport = (url, options = {}) =>
      transport(url, { ...options, deadline });
  }
  async exchange(code: string, verifier: string) {
    const c = googleConfig();
    return this.token(
      new URLSearchParams({
        grant_type: "authorization_code",
        code,
        code_verifier: verifier,
        client_id: c.clientId,
        client_secret: c.clientSecret,
        redirect_uri: c.callback,
      }),
    );
  }
  async refresh(refreshToken: string) {
    const c = googleConfig();
    return this.token(
      new URLSearchParams({
        grant_type: "refresh_token",
        refresh_token: refreshToken,
        client_id: c.clientId,
        client_secret: c.clientSecret,
      }),
    );
  }
  private async token(body: URLSearchParams) {
    const response = await this.transport(
      "https://oauth2.googleapis.com/token",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString(),
        origin: "https://oauth2.googleapis.com",
      },
    );
    if (response.status === 400)
      throw new ProviderError(
        "oauth",
        "Google OAuth expired or was revoked. Reconnect Google Search Console.",
      );
    const result = json<GoogleTokens>(response);
    if (
      !result.access_token ||
      !Number.isFinite(result.expires_in) ||
      result.expires_in <= 0
    )
      throw new ProviderError(
        "oauth",
        "Google did not return a valid access token.",
      );
    return {
      ...result,
      expires_at: Date.now() + Math.min(result.expires_in, 86400) * 1000,
    };
  }
  async properties(accessToken: string) {
    const result = json<{ siteEntry?: Property[] }>(
      await this.transport("https://www.googleapis.com/webmasters/v3/sites", {
        headers: { Authorization: `Bearer ${accessToken}` },
        origin: "https://www.googleapis.com",
      }),
    );
    return (result.siteEntry || [])
      .filter(
        (p) =>
          typeof p.siteUrl === "string" &&
          ["siteOwner", "siteFullUser", "siteRestrictedUser"].includes(
            p.permissionLevel,
          ),
      )
      .map((property) => ({
        siteUrl: property.siteUrl,
        permissionLevel: property.permissionLevel,
      }));
  }
  async fetch(
    accessToken: string,
    property: string,
    from: Date,
    to: Date,
  ): Promise<SearchObservation[]> {
    const rows: SearchObservation[] = [];
    // Finalised data only; retain query+page+date as one measurement grain.
    for (let startRow = 0; startRow < 250000; startRow += 25000) {
      const data = json<{
        rows?: {
          keys: string[];
          clicks: number;
          impressions: number;
          ctr: number;
          position: number;
        }[];
      }>(
        await this.transport(
          `https://www.googleapis.com/webmasters/v3/sites/${encodeURIComponent(property)}/searchAnalytics/query`,
          {
            method: "POST",
            origin: "https://www.googleapis.com",
            headers: {
              Authorization: `Bearer ${accessToken}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              startDate: from.toISOString().slice(0, 10),
              endDate: to.toISOString().slice(0, 10),
              dimensions: ["date", "query", "page"],
              type: "web",
              dataState: "final",
              rowLimit: 25000,
              startRow,
            }),
          },
        ),
      );
      const page = data.rows || [];
      for (const row of page) {
        if (
          row.keys?.length !== 3 ||
          !/^\d{4}-\d{2}-\d{2}$/.test(row.keys[0]) ||
          !Number.isSafeInteger(row.clicks) ||
          !Number.isSafeInteger(row.impressions) ||
          row.clicks < 0 ||
          row.impressions < row.clicks ||
          !Number.isFinite(row.position) ||
          row.position < 0 ||
          !Number.isFinite(row.ctr)
        )
          throw new ProviderError(
            "metrics",
            "Search Console returned invalid measurements.",
          );
        const date = new Date(`${row.keys[0]}T00:00:00Z`);
        if (!Number.isFinite(date.getTime()) || date < from || date > to)
          throw new ProviderError(
            "metrics",
            "Search Console returned data outside the requested range.",
          );
        const pageUrl = new URL(row.keys[2]);
        if (
          !["https:", "http:"].includes(pageUrl.protocol) ||
          pageUrl.username ||
          pageUrl.password
        )
          throw new ProviderError(
            "metrics",
            "Search Console returned an unsupported page URL.",
          );
        rows.push({
          date,
          query: row.keys[1],
          pageUrl: pageUrl.href,
          clicks: row.clicks,
          impressions: row.impressions,
          ctr: row.impressions ? row.clicks / row.impressions : 0,
          position: row.position,
        });
      }
      if (page.length < 25000) return rows;
    }
    throw new ProviderError(
      "partial",
      "Search Console sync exceeded the supported row limit. Use a shorter range.",
    );
  }
  async analyticsProperties(accessToken: string) {
    const result = json<{
      accountSummaries?: {
        account?: string;
        displayName?: string;
        propertySummaries?: { property?: string; displayName?: string }[];
      }[];
    }>(
      await this.transport(
        "https://analyticsadmin.googleapis.com/v1beta/accountSummaries?pageSize=200",
        {
          headers: { Authorization: `Bearer ${accessToken}` },
          origin: "https://analyticsadmin.googleapis.com",
        },
      ),
    );
    return (result.accountSummaries || []).flatMap((account) =>
      (account.propertySummaries || [])
        .filter(
          (property) =>
            typeof property.property === "string" &&
            /^properties\/\d+$/.test(property.property) &&
            typeof property.displayName === "string",
        )
        .map((property) => ({
          property: property.property!,
          displayName: property.displayName!,
          account: account.displayName || account.account || "Google Analytics",
        })),
    );
  }
  async fetchAnalytics(
    accessToken: string,
    property: string,
    from: Date,
    to: Date,
  ): Promise<Ga4Observation[]> {
    if (!/^properties\/\d+$/.test(property))
      throw new ProviderError("property", "Google Analytics property is invalid.");
    const result = json<{
      rows?: {
        dimensionValues?: { value?: string }[];
        metricValues?: { value?: string }[];
      }[];
    }>(
      await this.transport(
        `https://analyticsdata.googleapis.com/v1beta/${property}:runReport`,
        {
          method: "POST",
          origin: "https://analyticsdata.googleapis.com",
          headers: {
            Authorization: `Bearer ${accessToken}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            dateRanges: [
              {
                startDate: from.toISOString().slice(0, 10),
                endDate: to.toISOString().slice(0, 10),
              },
            ],
            dimensions: [
              { name: "date" },
              { name: "landingPagePlusQueryString" },
              { name: "sessionSource" },
              { name: "sessionMedium" },
            ],
            metrics: [
              { name: "sessions" },
              { name: "engagedSessions" },
              { name: "keyEvents" },
              { name: "totalRevenue" },
            ],
            limit: 100000,
            keepEmptyRows: false,
          }),
        },
      ),
    );
    const observations: Ga4Observation[] = [];
    for (const row of result.rows || []) {
      const dims = row.dimensionValues || [];
      const metrics = row.metricValues || [];
      if (dims.length !== 4 || metrics.length !== 4)
        throw new ProviderError("metrics", "Google Analytics returned an unexpected report shape.");
      const rawDate = dims[0]?.value || "";
      if (!/^\d{8}$/.test(rawDate))
        throw new ProviderError("metrics", "Google Analytics returned an invalid date.");
      const date = new Date(
        `${rawDate.slice(0, 4)}-${rawDate.slice(4, 6)}-${rawDate.slice(6, 8)}T00:00:00Z`,
      );
      const sessions = Number(metrics[0]?.value || 0);
      const engagedSessions = Number(metrics[1]?.value || 0);
      const conversions = Number(metrics[2]?.value || 0);
      const totalRevenue = Number(metrics[3]?.value || 0);
      if (
        ![sessions, engagedSessions, conversions, totalRevenue].every(Number.isFinite) ||
        sessions < 0 ||
        engagedSessions < 0 ||
        conversions < 0 ||
        totalRevenue < 0
      )
        throw new ProviderError("metrics", "Google Analytics returned invalid measurements.");
      observations.push({
        date,
        landingPage: dims[1]?.value || "(not set)",
        source: dims[2]?.value || "(direct)",
        medium: dims[3]?.value || "(none)",
        sessions,
        engagedSessions,
        conversions,
        totalRevenue,
      });
    }
    return observations;
  }
  async revoke(tokens: GoogleTokens) {
    const result = await this.transport(
      "https://oauth2.googleapis.com/revoke",
      {
        method: "POST",
        origin: "https://oauth2.googleapis.com",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          token: tokens.refresh_token || tokens.access_token,
        }).toString(),
      },
    );
    if (result.status !== 200 && result.status !== 400) json(result);
  }
}
