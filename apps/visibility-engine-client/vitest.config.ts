import { defineConfig } from "vitest/config";
import "dotenv/config";
export default defineConfig({
  test: {
    include: ["tests/**/*.test.ts"],
    testTimeout: 20000,
    hookTimeout: 20000,
    fileParallelism: false,
  },
  resolve: { alias: { "@": new URL(".", import.meta.url).pathname } },
});
