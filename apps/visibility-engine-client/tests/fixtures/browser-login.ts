import { expect, type Page, type Locator } from "@playwright/test";
export async function saveForm(page: Page, button: Locator) {
  // Successful ActionForm saves deliberately refresh the document to retain a
  // single CSP nonce. Wait for that refresh before testing or navigating again.
  await Promise.all([page.waitForEvent("load"), button.click()]);
}
export async function signIn(page: Page, email: string, password: string) {
  await page.goto("/login");
  await page.getByLabel("Work email").fill(email);
  await page.getByLabel("Password", { exact: true }).fill(password);
  for (let attempt = 0; attempt < 2; attempt++) {
    const responsePromise = page.waitForResponse(
      (response) =>
        response.url().includes("/api/auth/sign-in/email") &&
        response.request().method() === "POST",
    );
    await page
      .getByRole("button", { name: "Sign in to your workspace" })
      .click();
    const response = await responsePromise;
    if (response.status() === 429 && attempt === 0) {
      const seconds = Number(response.headers()["retry-after"] || 60);
      await new Promise((resolve) =>
        setTimeout(
          resolve,
          Math.min(60000, Math.max(1000, seconds * 1000)) + 150,
        ),
      );
      continue;
    }
    expect(response.ok()).toBe(true);
    await expect(page).toHaveURL(/\/workspace\/dashboard$/);
    return;
  }
}
