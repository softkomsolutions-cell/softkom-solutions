import { describe, it, expect } from "vitest";
import { randomUUID } from "node:crypto";
import { WordPressProvider } from "../lib/publishing/wordpress";
import {
  wordpressCredentials,
  localProviderTransport,
} from "./fixtures/live-transport";
describe("real disposable WordPress REST API", () => {
  it("authenticates Application Passwords and rejects wrong or insufficient credentials", async () => {
    const credentials = wordpressCredentials();
    expect(
      (
        await new WordPressProvider(
          credentials,
          localProviderTransport,
        ).verify()
      ).accountId,
    ).toBeTruthy();
    await expect(
      new WordPressProvider(
        { ...credentials, password: "wrong-application-password" },
        localProviderTransport,
      ).verify(),
    ).rejects.toThrow("Authentication failed");
    await expect(
      new WordPressProvider(
        {
          ...credentials,
          username: credentials.subscriber,
          password: credentials.subscriberPassword,
        },
        localProviderTransport,
      ).verify(),
    ).rejects.toThrow(/permission|able to create/);
  });
  it("publishes, verifies the public page and retries against the same WordPress ID", async () => {
    const credentials = wordpressCredentials();
    const provider = new WordPressProvider(credentials, localProviderTransport);
    const input = {
      title: "Real WordPress approved fixture",
      html: "<p>Exact approved content from the disposable integration test.</p>",
      slug: "real-wordpress-test",
      metaTitle: "Approved SEO title",
      metaDescription: "Real WordPress integration fixture",
      funnel: "https://wordpress.visibility.test/contact/",
      idempotencyKey: randomUUID(),
    };
    const first = await provider.publish(input);
    try {
      await provider.verifyPublication(first, input);
      const second = await provider.publish({
        ...input,
        externalId: first.externalId,
      });
      expect(second.externalId).toBe(first.externalId);
      expect(second.liveUrl).toBe(first.liveUrl);
      await provider.verifyPublication(second, input);
    } finally {
      await localProviderTransport(
        `https://wordpress.visibility.test/wp-json/wp/v2/pages/${first.externalId}?force=true`,
        {
          method: "DELETE",
          headers: {
            Authorization: `Basic ${Buffer.from(`${credentials.username}:${credentials.password}`).toString("base64")}`,
          },
        },
      );
    }
  });
});
