import { defineConfig } from "@playwright/test";
import "dotenv/config";
export default defineConfig({
  testDir: "./tests/browser",
  fullyParallel: false,
  workers: 1,
  retries: 0,
  timeout: 45000,
  use: {
    baseURL: process.env.BETTER_AUTH_URL || "http://localhost:3000",
    headless: true,
    launchOptions: {
      ...(process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE
        ? { executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE }
        : {}),
      args: ["--no-sandbox"],
    },
    trace: "retain-on-failure",
    screenshot: "only-on-failure",
  },
  webServer: {
    command: "npm run start -- --hostname 0.0.0.0",
    url: "http://localhost:3000/login",
    reuseExistingServer: false,
    timeout: 90000,
  },
});
