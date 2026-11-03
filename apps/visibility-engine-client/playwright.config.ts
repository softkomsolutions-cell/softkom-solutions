import { readFileSync } from "node:fs";
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
      args: [
        "--no-sandbox",
        `--ignore-certificate-errors-spki-list=${readFileSync(".test-tls/spki", "utf8")}`,
      ],
    },
    trace: "retain-on-failure",
    screenshot: "only-on-failure",
  },
  webServer: [
    {
      command: "node tests/fixtures/google-server.mjs",
      url: "http://127.0.0.1:8899/health",
      reuseExistingServer: false,
    },
    {
      command:
        "node --import ./tests/fixtures/preload.mjs ./node_modules/next/dist/bin/next start --hostname 0.0.0.0",
      env: {
        BETTER_AUTH_URL: "https://localhost:3443",
        GOOGLE_CLIENT_ID: "controlled-client",
        GOOGLE_CLIENT_SECRET: "controlled-secret",
        GOOGLE_CALLBACK_URL:
          "https://localhost:3443/api/connections/google/callback",
      },
      url: "http://localhost:3000/login",
      reuseExistingServer: false,
      timeout: 90000,
    },
    {
      command: "node tests/fixtures/https-proxy.mjs",
      url: "https://localhost:3443/login",
      reuseExistingServer: false,
    },
  ],
});
