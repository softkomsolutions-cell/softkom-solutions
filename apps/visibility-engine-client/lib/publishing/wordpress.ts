import { createHash } from "node:crypto";
import {
  destination,
  http,
  json,
  ProviderError,
  type Transport,
} from "../connections/http";
import type {
  PublishInput,
  PublishResult,
  PublishingProvider,
} from "./providers";
export type WordPressCredentials = {
  siteUrl: string;
  username: string;
  password: string;
};
type RemotePage = {
  id: number;
  link: string;
  status: string;
  slug: string;
  title?: { rendered: string };
  content?: { raw?: string; rendered?: string };
};
export class WordPressProvider implements PublishingProvider {
  available = true;
  private base: URL;
  private deadline = Date.now() + 180000;
  constructor(
    private credentials: WordPressCredentials,
    private transport: Transport = http,
  ) {
    this.base = destination(
      credentials.siteUrl.endsWith("/")
        ? credentials.siteUrl
        : credentials.siteUrl + "/",
    );
  }
  private async api<T>(path: string, method = "GET", body?: unknown) {
    return json<T>(
      await this.transport(new URL(`wp-json/${path}`, this.base).href, {
        method,
        origin: this.base.origin,
        deadline: this.deadline,
        headers: {
          Authorization: `Basic ${Buffer.from(`${this.credentials.username}:${this.credentials.password}`).toString("base64")}`,
          "Content-Type": "application/json",
        },
        ...(body ? { body: JSON.stringify(body) } : {}),
      }),
    );
  }
  async verify() {
    const index = await this.api<{ namespaces?: string[] }>("");
    if (!index.namespaces?.includes("wp/v2"))
      throw new ProviderError("wordpress", "WordPress REST API unavailable.");
    const user = await this.api<{
      id: number;
      name: string;
      capabilities?: Record<string, boolean>;
    }>("wp/v2/users/me?context=edit");
    if (
      !user.id ||
      !user.capabilities?.edit_pages ||
      !user.capabilities?.publish_pages ||
      !user.capabilities?.edit_published_pages
    )
      throw new ProviderError(
        "permission",
        "The WordPress account must be able to create, publish and edit pages.",
      );
    return {
      accountLabel: String(user.name).slice(0, 200),
      accountId: String(user.id),
    };
  }
  async publish(input: PublishInput): Promise<PublishResult> {
    await this.verify();
    const marker = `visibility-engine:${createHash("sha256").update(input.idempotencyKey).digest("hex").slice(0, 24)}`;
    // Keep the public URL clean while retaining a stable hidden marker for
    // idempotent recovery after lost API responses or worker restarts.
    const matches = await this.api<RemotePage[]>(
      `wp/v2/pages?slug=${encodeURIComponent(input.slug)}&context=edit&status=publish,draft,pending,private&per_page=2`,
    );
    if (!Array.isArray(matches) || matches.length > 1)
      throw new ProviderError(
        "conflict",
        "The approved slug matches multiple WordPress pages. Manual recovery is required.",
      );
    const matched = matches[0];
    const matchedMarker =
      matched?.content?.raw?.includes(marker) ||
      matched?.content?.rendered?.includes(marker);
    if (
      matched &&
      !matchedMarker &&
      (!input.externalId || String(matched.id) !== input.externalId)
    )
      throw new ProviderError(
        "conflict",
        "The approved slug is already used by another WordPress page. Choose a different slug or reconcile the existing page.",
      );
    if (
      input.externalId &&
      matched &&
      String(matched.id) !== input.externalId
    )
      throw new ProviderError(
        "conflict",
        "Remote publication identity changed. Manual recovery is required.",
      );
    const id =
      input.externalId || (matchedMarker && matched ? String(matched.id) : undefined);
    const body = `${input.html}\n<!-- ${marker} -->`;
    const page = await this.api<RemotePage>(
      `wp/v2/pages${id ? `/${encodeURIComponent(id)}` : ""}`,
      "POST",
      {
        title: input.title,
        content: body,
        slug: input.slug,
        status: "publish",
        excerpt: input.metaDescription,
      },
    );
    if (
      !Number.isSafeInteger(page.id) ||
      page.id < 1 ||
      (id && String(page.id) !== id) ||
      page.status !== "publish" ||
      page.slug !== input.slug
    )
      throw new ProviderError(
        "publication",
        "WordPress did not confirm the expected published page.",
      );
    const live = destination(page.link);
    if (live.origin !== this.base.origin)
      throw new ProviderError(
        "verification",
        "WordPress returned a public URL for another site.",
      );
    return {
      externalId: String(page.id),
      liveUrl: live.href,
      publishedAt: new Date(),
      response: {
        status: "publish",
        marker,
        metadata:
          "WordPress title and excerpt; SEO-plugin metadata not configured",
      },
    };
  }
  async verifyPublication(result: PublishResult, input: PublishInput) {
    const page = await this.api<RemotePage>(
      `wp/v2/pages/${encodeURIComponent(result.externalId)}?context=edit`,
    );
    const marker = result.response.marker;
    if (
      String(page.id) !== result.externalId ||
      page.status !== "publish" ||
      !marker ||
      page.slug !== input.slug ||
      !page.content?.raw?.includes(marker) ||
      !page.content.raw.includes(input.html)
    )
      throw new ProviderError(
        "verification",
        "Published page identity or approved content could not be verified.",
        true,
      );
    const response = await this.transport(result.liveUrl, {
      origin: this.base.origin,
      deadline: this.deadline,
    });
    if (response.status !== 200 || !response.body.includes(marker))
      throw new ProviderError(
        "verification",
        "Public page could not be verified. The remote page is retained for retry.",
        true,
      );
  }
}
