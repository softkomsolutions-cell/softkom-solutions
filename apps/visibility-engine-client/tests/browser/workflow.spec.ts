import { signIn, saveForm } from "../fixtures/browser-login";
import { test, expect, type BrowserContext } from "@playwright/test";
import { randomUUID } from "node:crypto";
import { hashPassword } from "better-auth/crypto";
import { db } from "../../lib/db";
import { mkdir } from "node:fs/promises";
test.describe.configure({ mode: "serial" });
const password = `Browser-${randomUUID()}!`;
const createdUsers: string[] = [];
const createdOrgs: string[] = [];
let managerEmail: string,
  clientEmail: string,
  newEmail: string,
  foreignEmail: string;
let orgId: string, foreignContentId: string, contentId: string;
let managerState: Awaited<ReturnType<BrowserContext["storageState"]>>;
let clientState: typeof managerState,
  newState: typeof managerState,
  foreignState: typeof managerState;

test.beforeAll(async ({ playwright }) => {
  test.setTimeout(90000);
  const org = await db.organisation.create({
    data: {
      name: "Example Client · test workspace",
      slug: `browser-${randomUUID()}`,
    },
  });
  orgId = org.id;
  createdOrgs.push(org.id);
  const other = await db.organisation.create({
    data: {
      name: "Private other organisation",
      slug: `browser-${randomUUID()}`,
    },
  });
  createdOrgs.push(other.id);
  const site = await db.site.create({
    data: {
      organisationId: org.id,
      name: "Example client website",
      baseUrl: "https://example.test",
      connections: {
        create: [
          { provider: "WORDPRESS", status: "REQUIRES_SETUP" },
          { provider: "GOOGLE_SEARCH_CONSOLE", status: "COMING_SOON" },
        ],
      },
    },
  });
  const otherSite = await db.site.create({
    data: {
      organisationId: other.id,
      name: "Private site",
      baseUrl: "https://private.example.test",
    },
  });
  foreignContentId = (
    await db.contentItem.create({
      data: {
        siteId: otherSite.id,
        title: "Confidential other-client strategy",
        slug: "confidential",
        body: "<p>Private record not visible to anyone else.</p>",
      },
    })
  ).id;
  async function user(
    name: string,
    organisationId?: string,
    role: "OWNER" | "CLIENT" = "CLIENT",
  ) {
    const email = `${randomUUID()}@example.test`;
    const u = await db.user.create({
      data: { name, email, emailVerified: true },
    });
    createdUsers.push(u.id);
    await db.account.create({
      data: {
        id: randomUUID(),
        userId: u.id,
        providerId: "credential",
        accountId: u.id,
        password: await hashPassword(password),
      },
    });
    if (organisationId)
      await db.membership.create({
        data: { userId: u.id, organisationId, role },
      });
    return email;
  }
  managerEmail = await user("Test manager", org.id, "OWNER");
  clientEmail = await user("Test client", org.id);
  newEmail = await user("New client");
  foreignEmail = await user("Other client", other.id, "OWNER");
  for (const [email, save] of [
    [clientEmail, (s: typeof managerState) => (clientState = s)],
    [newEmail, (s: typeof managerState) => (newState = s)],
    [foreignEmail, (s: typeof managerState) => (foreignState = s)],
  ] as const) {
    const request = await playwright.request.newContext({
      baseURL: process.env.BETTER_AUTH_URL!,
    });
    let response = await request.post("/api/auth/sign-in/email", {
      data: { email, password },
      headers: { Origin: process.env.BETTER_AUTH_URL! },
    });
    if (response.status() === 429) {
      // Respect the real production rate limit between repeated local suites.
      const retry = Math.min(
        60000,
        Math.max(1000, Number(response.headers()["retry-after"] || 60) * 1000),
      );
      await new Promise((resolve) => setTimeout(resolve, retry));
      response = await request.post("/api/auth/sign-in/email", {
        data: { email, password },
        headers: { Origin: process.env.BETTER_AUTH_URL! },
      });
    }
    expect(response.ok(), await response.text()).toBe(true);
    save(await request.storageState());
    await request.dispose();
  }
  expect(site.id).toBeTruthy();
});
test.afterAll(async () => {
  // Include the organisation created by the onboarding scenario.
  const memberships = await db.membership.findMany({
    where: { userId: { in: createdUsers } },
  });
  const orgs = Array.from(
    new Set([...createdOrgs, ...memberships.map((m) => m.organisationId)]),
  );
  await db.contentComment.deleteMany({
    where: { authorId: { in: createdUsers } },
  });
  await db.leadAttribution.deleteMany({
    where: { site: { organisationId: { in: orgs } } },
  });
  await db.contentItem.deleteMany({
    where: { site: { organisationId: { in: orgs } } },
  });
  await db.organisation.deleteMany({ where: { id: { in: orgs } } });
  await db.user.deleteMany({ where: { id: { in: createdUsers } } });
  await db.$disconnect();
});
test("unauthenticated routes redirect, signup is disabled, cross-origin sign-in is rejected", async ({
  page,
  request,
}) => {
  await page.goto("/workspace/dashboard");
  await expect(page).toHaveURL(/\/login$/);
  const signup = await request.post("/api/auth/sign-up/email", {
    data: { name: "Intruder", email: "uninvited@example.test", password },
    headers: { Origin: process.env.BETTER_AUTH_URL! },
  });
  expect(signup.ok()).toBe(false);
  const csrf = await request.post("/api/auth/sign-in/email", {
    data: { email: managerEmail, password },
    headers: { Origin: "https://attacker.example" },
  });
  expect(csrf.status()).toBe(403);
});
test("real login hydrates under CSP and opens a truthful empty workspace", async ({
  page,
}) => {
  const errors: string[] = [];
  page.on("pageerror", (e) => errors.push(e.message));
  test.setTimeout(90000);
  await signIn(page, managerEmail, password);
  await expect(
    page.getByRole("heading", { name: "Your visibility, with purpose." }),
  ).toBeVisible();
  await expect(page.getByText("4,820")).toHaveCount(0);
  await expect(
    page.getByText("Good opportunities start with your business."),
  ).toBeVisible();
  const sessionCookie = (await page.context().cookies()).find((cookie) =>
    cookie.name.endsWith("session_token"),
  );
  expect(sessionCookie?.httpOnly).toBe(true);
  expect(sessionCookie?.secure).toBe(true);
  expect(sessionCookie?.sameSite).toBe("Lax");
  managerState = await page.context().storageState();
  expect(errors).toEqual([]);
});
test("a client completes all four onboarding steps without a fake integration", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: newState });
  const page = await context.newPage();
  await page.goto("/workspace/dashboard");
  await expect(page).toHaveURL(/\/onboarding$/);
  await page.getByLabel("Business name").fill("New Test Business");
  await page.getByRole("button", { name: "Continue" }).click();
  await page.getByLabel("Site name", { exact: true }).fill("New business site");
  await page.getByLabel("Website URL").fill("https://new.example.test");
  await page.getByRole("button", { name: "Continue" }).click();
  await page
    .getByLabel("Key services or products")
    .fill("Business continuity reviews");
  await page.getByLabel("Target customers").fill("Business owners");
  await page.getByLabel("Geographic market").fill("South Africa");
  await page
    .getByLabel("Primary conversion goal")
    .fill("Book a business review");
  await page.getByRole("button", { name: "Continue" }).click();
  await expect(
    page.getByText("Connect after setup", { exact: true }),
  ).toBeVisible();
  await page.getByRole("button", { name: "Open your workspace" }).click();
  await expect(page).toHaveURL(/\/workspace\/dashboard$/);
  await expect(
    page.locator(".topbar strong").filter({ hasText: "New Test Business" }),
  ).toBeVisible();
  await context.close();
});
test("manager records a reasoned opportunity and proposes real content", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: managerState });
  const page = await context.newPage();
  await page.goto("/workspace/opportunities");
  await page.getByText("Add a researched opportunity", { exact: true }).click();
  await page.getByLabel("Topic / title").fill("Recover missed enquiries");
  await page.getByLabel("URL slug").fill("recover-missed-enquiries");
  await page.getByLabel("Cluster", { exact: true }).fill("Revenue Recovery");
  await page
    .getByLabel("Funnel destination / client offer")
    .fill("lost-lead-recovery");
  await page
    .getByLabel("Business problem", { exact: true })
    .fill("Qualified enquiries are not consistently followed up.");
  await page
    .getByLabel("Why Softkom recommends this")
    .fill(
      "The client offers lead recovery and can give owners a measurable next action. Evidence is from the test business brief, not a demand estimate.",
    );
  await saveForm(
    page,
    page.getByRole("button", { name: "Add opportunity", exact: true }),
  );
  await expect(page.locator(".topic-card").first()).toContainText(
    "Recover missed enquiries",
  );
  await page.locator(".topic-card").first().click();
  await expect(
    page.getByRole("heading", { name: "The commercial reasoning" }),
  ).toBeVisible();
  await page.goto("/workspace/content");
  await page
    .getByText("Propose content for client review", { exact: true })
    .click();
  await page
    .getByLabel("Title", { exact: true })
    .fill("A practical guide to enquiry follow-up");
  await page.getByLabel("Slug", { exact: true }).fill("enquiry-follow-up");
  await page
    .getByLabel("Funnel destination", { exact: true })
    .fill("lost-lead-recovery");
  await page
    .getByLabel("SEO title", { exact: true })
    .fill("Recover missed business enquiries");
  await page
    .getByLabel("Meta description", { exact: true })
    .fill(
      "A practical process to give each enquiry a timely, useful next step.",
    );
  await page
    .getByLabel("Page content (safe HTML supported)")
    .fill(
      "<p>Every genuine enquiry deserves a considered response.</p><h2>Start with a clear owner</h2><p>Agree who follows up and when.</p>",
    );
  await page.getByLabel("Planned date").fill("2026-11-12T09:00");
  await page
    .getByLabel("Assigned reviewer")
    .selectOption({ label: "Test client" });
  await saveForm(
    page,
    page.getByRole("button", { name: "Send for review", exact: true }),
  );
  await expect(
    page.getByRole("link", {
      name: "A practical guide to enquiry follow-up",
      exact: true,
    }),
  ).toBeVisible();
  contentId = (
    await db.contentItem.findFirstOrThrow({
      where: { site: { organisationId: orgId } },
    })
  ).id;
  await page.goto(`/workspace/content/${contentId}`);
  await expect(
    page
      .locator(".content-preview")
      .getByText("Every genuine enquiry deserves a considered response."),
  ).toBeVisible();
  await context.close();
});
test("client requests changes, sees history, and cannot see manager controls", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: clientState });
  const page = await context.newPage();
  await page.goto(`/workspace/content/${contentId}`);
  await page.getByLabel("Review action").selectOption("CHANGES_REQUESTED");
  await page
    .getByLabel("Comment (required")
    .fill("Please make the follow-up ownership explicit.");
  await saveForm(
    page,
    page.getByRole("button", { name: "Save review decision", exact: true }),
  );

  await expect(page.locator(".history-entry")).toContainText(
    "Please make the follow-up ownership explicit.",
  );
  await expect(
    page.getByText("Prepare a new revision", { exact: true }),
  ).toHaveCount(0);
  await expect(
    page.getByRole("button", { name: "Publish approved revision" }),
  ).toHaveCount(0);
  await context.close();
});
test("a new revision is approved, an unavailable publication is recorded truthfully", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: managerState });
  const page = await context.newPage();
  await page.goto(`/workspace/content/${contentId}`);
  await page.getByText("Prepare a new revision", { exact: true }).click();
  await page
    .getByLabel("Page content", { exact: true })
    .fill("<p>Give every enquiry a named owner and a useful next step.</p>");
  await saveForm(
    page,
    page.getByRole("button", {
      name: "Save revision & request review",
      exact: true,
    }),
  );
  await expect(page.locator(".content-preview")).toContainText(
    "Give every enquiry a named owner and a useful next step.",
  );
  const clientContext = await browser.newContext({ storageState: clientState });
  const review = await clientContext.newPage();
  await review.goto(`/workspace/content/${contentId}`);
  await saveForm(
    review,
    review.getByRole("button", { name: "Save review decision", exact: true }),
  );

  await expect(review.locator(".history-entry").first()).toContainText(
    "Approved",
  );
  await clientContext.close();
  await page.reload();
  await saveForm(
    page,
    page.getByRole("button", {
      name: "Publish approved revision",
      exact: true,
    }),
  );

  await expect(page.locator(".publication")).toContainText("FAILED", {
    ignoreCase: true,
  });
  await expect(page.getByRole("link", { name: "Open live page" })).toHaveCount(
    0,
  );
  await context.close();
});
test("foreign IDs, site filters and forged organisation cookies never reveal another client", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: managerState });
  const page = await context.newPage();
  await page.goto(`/workspace/content/${foreignContentId}`);
  await expect(
    page.getByRole("heading", { name: "There’s nothing here to open." }),
  ).toBeVisible();
  await expect(
    page.getByText("Confidential other-client strategy"),
  ).toHaveCount(0);
  const site = await db.site.findFirstOrThrow({
    where: { organisationId: createdOrgs[1] },
  });
  await page.goto(`/workspace/content?site=${site.id}`);
  await expect(
    page.getByRole("heading", { name: "We couldn’t open this view." }),
  ).toBeVisible();
  await expect(
    page.getByText("Confidential other-client strategy"),
  ).toHaveCount(0);
  await page.goto(`/workspace/content/${contentId}`);
  const reviewForm = page
    .locator("form")
    .filter({ has: page.locator('input[name="operation"][value="review"]') });
  let forgedPayloadSent = false;
  await page.route("**/workspace/content/**", async (route) => {
    const request = route.request();
    if (request.method() === "POST" && request.headers()["next-action"]) {
      const body = request.postData() || "";
      expect(body).toContain(contentId);
      forgedPayloadSent = true;
      await route.continue({
        postData: body.replaceAll(contentId, foreignContentId),
      });
    } else await route.continue();
  });
  await reviewForm
    .getByRole("button", { name: "Save review decision" })
    .click();
  await expect(reviewForm.getByRole("alert")).toContainText(
    "not found in this workspace",
  );
  expect(forgedPayloadSent).toBe(true);
  await page.unroute("**/workspace/content/**");
  expect(
    (
      await db.contentItem.findUniqueOrThrow({
        where: { id: foreignContentId },
      })
    ).status,
  ).toBe("DRAFT");
  await context.addCookies([
    {
      name: "visibility-org",
      value: createdOrgs[1],
      domain: "localhost",
      path: "/",
    },
  ]);
  await page.goto("/workspace/dashboard");
  await expect(
    page.getByText("Private other organisation", { exact: true }),
  ).toHaveCount(0);
  await context.close();
  const other = await browser.newContext({ storageState: foreignState });
  const otherPage = await other.newPage();
  await otherPage.goto(`/workspace/content/${contentId}`);
  await expect(
    otherPage.getByRole("heading", { name: "There’s nothing here to open." }),
  ).toBeVisible();
  await other.close();
});
test("desktop and mobile views render, filters work, and screenshots capture the product", async ({
  browser,
}) => {
  const context = await browser.newContext({
    storageState: managerState,
    viewport: { width: 1440, height: 1000 },
  });
  const page = await context.newPage();
  const errors: string[] = [];
  page.on("pageerror", (e) => errors.push(e.message));
  await page.goto("/workspace/dashboard");
  await expect(page.locator(".hero-panel")).toBeVisible();
  await expect(page.locator(".skeleton")).toHaveCount(0);
  await mkdir("../../docs/visibility-engine-client-v1", { recursive: true });
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/dashboard-desktop.png",
    fullPage: true,
  });
  await page.goto("/workspace/calendar?month=2026-11");
  await expect(
    page.getByRole("link", { name: /A practical guide/ }),
  ).toBeVisible();
  await page.getByLabel("Status", { exact: true }).selectOption("APPROVED");
  await page.getByRole("button", { name: "Apply filters" }).click();
  await expect(
    page.getByRole("link", { name: /A practical guide/ }),
  ).toBeVisible();
  for (const section of [
    "analytics",
    "ai",
    "leads",
    "strategy",
    "connections",
    "reports",
    "settings",
  ]) {
    await page.goto(`/workspace/${section}`);
    await expect(page.locator("h1")).toBeVisible();
    await expect(
      page.getByRole("heading", { name: "We couldn’t open this view." }),
    ).toHaveCount(0);
  }
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto("/workspace/dashboard");
  await expect(page.locator(".hero-panel")).toBeVisible();
  await expect(page.locator(".skeleton")).toHaveCount(0);
  expect(
    await page.evaluate(
      () => document.documentElement.scrollWidth <= window.innerWidth,
    ),
  ).toBe(true);
  await expect(
    page.getByRole("navigation", { name: "Workspace" }),
  ).toBeVisible();
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/dashboard-mobile.png",
    fullPage: true,
  });
  await page.goto(`/workspace/content/${contentId}`);
  await expect(page.locator(".content-preview")).toBeVisible();
  expect(
    await page.evaluate(
      () => document.documentElement.scrollWidth <= window.innerWidth,
    ),
  ).toBe(true);
  await page.screenshot({
    path: "../../docs/visibility-engine-client-v1/content-review-mobile.png",
    fullPage: true,
  });
  expect(errors).toEqual([]);
  await context.close();
});
test("logout revokes the database session and protected routes require login again", async ({
  browser,
}) => {
  const context = await browser.newContext({ storageState: clientState });
  const page = await context.newPage();
  await page.goto("/workspace/dashboard");
  await page.getByRole("button", { name: "Sign out", exact: true }).click();
  await expect(page).toHaveURL(/\/login$/);
  await page.goto("/workspace/dashboard");
  await expect(page).toHaveURL(/\/login$/);
  await context.close();
});
