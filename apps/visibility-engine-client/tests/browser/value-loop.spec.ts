import { signIn, saveForm } from "../fixtures/browser-login";
import { test, expect } from "@playwright/test";
import { randomUUID } from "node:crypto";
import { mkdir } from "node:fs/promises";
import { hashPassword } from "better-auth/crypto";
import { db } from "../../lib/db";
import { processPublication } from "../../lib/publishing/worker";
import { processSync, syncWindow } from "../../lib/analytics/sync";
import {
  wordpressCredentials,
  localProviderTransport,
} from "../fixtures/live-transport";
const password = `Fixture-${randomUUID()}-only`;
let organisationId: string,
  userId: string,
  foreignOrgId: string,
  foreignSiteId: string;
test.afterAll(async () => {
  if (organisationId) {
    await db.contentItem.deleteMany({ where: { site: { organisationId } } });
    await db.organisation.deleteMany({
      where: { id: { in: [organisationId, foreignOrgId].filter(Boolean) } },
    });
  }
  if (userId) await db.user.deleteMany({ where: { id: userId } });
  await db.$disconnect();
});
test("approved revision → real WordPress → controlled Google OAuth → persisted page metrics", async ({
  page,
  request,
}) => {
  test.setTimeout(120000);
  const organisation = await db.organisation.create({
    data: {
      name: "Controlled Value Loop Organisation",
      slug: randomUUID(),
      brandedTerms: "Visibility Fixture",
    },
  });
  organisationId = organisation.id;
  const user = await db.user.create({
    data: {
      email: `${randomUUID()}@example.test`,
      name: "Value Loop Owner",
      emailVerified: true,
    },
  });
  userId = user.id;
  await db.account.create({
    data: {
      id: randomUUID(),
      userId,
      providerId: "credential",
      accountId: userId,
      password: await hashPassword(password),
    },
  });
  await db.membership.create({
    data: { organisationId, userId, role: "OWNER" },
  });
  const credentials = wordpressCredentials();
  const site = await db.site.create({
    data: {
      organisationId,
      name: "Disposable WordPress site",
      baseUrl: credentials.siteUrl,
      cms: "WORDPRESS",
    },
  });
  const foreign = await db.organisation.create({
    data: { name: "Private foreign tenant", slug: randomUUID() },
  });
  foreignOrgId = foreign.id;
  foreignSiteId = (
    await db.site.create({
      data: {
        organisationId: foreign.id,
        name: "Private foreign site",
        baseUrl: "https://foreign.example",
      },
    })
  ).id;
  await signIn(page, user.email, password);
  await page.goto("/workspace/connections");
  await page.getByText("Connect WordPress", { exact: true }).click();
  await page.getByLabel("WordPress username").fill(credentials.username);
  await page.getByLabel("Application password").fill(credentials.password);
  await saveForm(
    page,
    page.getByRole("button", {
      name: "Verify & connect WordPress",
      exact: true,
    }),
  );
  await expect(
    page
      .locator(".connection-card")
      .first()
      .getByText("Connected", { exact: true }),
  ).toBeVisible();
  await Promise.all([
    page.waitForEvent("load"),
    page.getByRole("button", { name: "Test connection", exact: true }).click(),
  ]);
  await expect(page.getByLabel("Application password")).toHaveValue("");
  const topic = await db.topic.create({
    data: {
      siteId: site.id,
      title: "A measured commercial opportunity",
      slug: "measured-opportunity",
      cluster: "Revenue Recovery",
      intent: "COMMERCIAL",
      reasoning: "Controlled business brief",
      funnel: "/contact",
    },
  });
  const content = await db.contentItem.create({
    data: {
      siteId: site.id,
      topicId: topic.id,
      title: "The approved visibility page",
      slug: "approved-visibility-page",
      body: "<p>The exact approved browser-test revision.</p>",
      metaTitle: "Approved visibility SEO title",
      metaDesc: "Controlled browser value-loop content",
      funnel: "/contact",
      status: "CLIENT_REVIEW",
      reviewerId: userId,
      revisions: {
        create: {
          number: 1,
          title: "The approved visibility page",
          body: "<p>The exact approved browser-test revision.</p>",
          slug: "approved-visibility-page",
          funnel: "/contact",
          metaTitle: "Approved visibility SEO title",
          metaDesc: "Controlled browser value-loop content",
          authorId: userId,
        },
      },
    },
  });
  await page.goto(`/workspace/content/${content.id}`);
  await saveForm(
    page,
    page.getByRole("button", { name: "Save review decision", exact: true }),
  );
  await expect(page.locator(".history-entry").first()).toContainText(
    "Approved",
  );
  await saveForm(
    page,
    page.getByRole("button", {
      name: "Publish approved revision",
      exact: true,
    }),
  );
  await expect(page.locator(".publication")).toContainText("Pending");
  const job = await db.publication.findFirstOrThrow({
    where: { contentId: content.id },
  });
  await processPublication(job.id, localProviderTransport);
  await page.reload();
  await expect(page.locator(".publication")).toContainText("Live");
  const live = await page
    .getByRole("link", { name: "Open live page" })
    .getAttribute("href");
  expect(live).toMatch(/^https:\/\/wordpress.visibility.test\//);
  expect(await processPublication(job.id, localProviderTransport)).toBe(false);
  await page.goto("/workspace/connections");
  await page.route("https://accounts.google.com/**", async (route) => {
    const url = new URL(route.request().url());
    expect(url.searchParams.get("scope")).toBe(
      "https://www.googleapis.com/auth/webmasters.readonly",
    );
    expect(url.searchParams.get("code_challenge_method")).toBe("S256");
    await request.post("http://127.0.0.1:8899/__control/consent", {
      data: { challenge: url.searchParams.get("code_challenge") },
    });
    const callback = new URL(url.searchParams.get("redirect_uri")!);
    callback.searchParams.set("state", url.searchParams.get("state")!);
    callback.searchParams.set("code", "controlled-google-code");
    await route.fulfill({
      status: 302,
      headers: { location: callback.href },
      body: "",
    });
  });
  await page
    .getByRole("button", { name: "Connect Google Search Console", exact: true })
    .click();
  await expect(page.getByLabel("Search Console property")).toBeVisible();
  await page
    .getByLabel("Search Console property")
    .selectOption("https://wordpress.visibility.test/");
  await saveForm(
    page,
    page.getByRole("button", { name: "Select property", exact: true }),
  );
  await expect(
    page
      .locator(".connection-card")
      .nth(1)
      .getByText("Connected", { exact: true }),
  ).toBeVisible();
  const date = syncWindow().to.toISOString().slice(0, 10);
  await request.post("http://127.0.0.1:8899/__control/metrics", {
    data: [
      {
        keys: [date, "business automation", live],
        clicks: 14,
        impressions: 200,
        ctr: 0.07,
        position: 3.2,
      },
    ],
  });
  await saveForm(
    page,
    page.getByRole("button", { name: "Sync search data", exact: true }),
  );
  await expect(page.getByText("Search sync: Pending")).toBeVisible();
  const sync = await db.searchSync.findFirstOrThrow({
    where: { connection: { siteId: site.id } },
  });
  await processSync(sync.id, localProviderTransport);
  await page.goto(`/workspace/analytics?month=${date.slice(0, 7)}`);
  await expect(
    page.getByRole("heading", { name: "Your published content, measured" }),
  ).toBeVisible();
  await expect(
    page.getByRole("row").filter({ hasText: "The approved visibility page" }),
  ).toContainText("14");
  await expect(
    page.getByRole("row").filter({ hasText: "The approved visibility page" }),
  ).toContainText("200");
  await expect(
    page.getByRole("row").filter({ hasText: "The approved visibility page" }),
  ).toContainText("7.00%");
  await expect(
    page.getByText("business automation", { exact: true }).first(),
  ).toBeVisible();
  await expect(
    page.getByText(/Today’s results are not complete/),
  ).toBeVisible();
  await mkdir("../../docs/visibility-engine-client-v1", { recursive: true });
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/phase2-search-desktop.png",
    fullPage: true,
  });
  await page.setViewportSize({ width: 390, height: 844 });
  await expect(page.locator("h1")).toBeVisible();
  expect(
    await page.evaluate(
      () => document.documentElement.scrollWidth <= innerWidth,
    ),
  ).toBe(true);
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/phase2-search-mobile.png",
    fullPage: true,
  });
  await page.goto(`/workspace/analytics?site=${foreignSiteId}`);
  await expect(
    page.getByRole("heading", { name: "We couldn’t open this view." }),
  ).toBeVisible();
  await expect(page.getByText("Private foreign site")).toHaveCount(0);
  await page.goto("/workspace/connections");
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/phase2-connections-mobile.png",
    fullPage: true,
  });
  await page.getByRole("button", { name: "Sign out", exact: true }).click();
  await expect(page).toHaveURL(/\/login$/);
  await localProviderTransport(
    `https://wordpress.visibility.test/wp-json/wp/v2/pages/${(await db.publication.findUniqueOrThrow({ where: { id: job.id } })).externalId}?force=true`,
    {
      method: "DELETE",
      headers: {
        Authorization: `Basic ${Buffer.from(`${credentials.username}:${credentials.password}`).toString("base64")}`,
      },
    },
  );
});
