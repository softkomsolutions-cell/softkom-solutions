import { http, json, ProviderError, type Transport } from "../connections/http";
import type { SearchObservation } from "./providers";
export type GoogleTokens = {
  access_token: string;
  refresh_token?: string;
  expires_in: number;
  expires_at?: number;
};
export type Property = { siteUrl: string; permissionLevel: string };
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
