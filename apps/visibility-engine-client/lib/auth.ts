import { betterAuth } from "better-auth";
import { prismaAdapter } from "better-auth/adapters/prisma";
import { db } from "./db";

if (
  process.env.NODE_ENV === "production" &&
  (process.env.BETTER_AUTH_SECRET?.length ?? 0) < 32
) {
  throw new Error(
    "Configure a strong BETTER_AUTH_SECRET before starting the application.",
  );
}

export const auth = betterAuth({
  appName: "Softkom Visibility Engine",
  baseURL: process.env.BETTER_AUTH_URL,
  secret: process.env.BETTER_AUTH_SECRET,
  database: prismaAdapter(db, { provider: "postgresql" }),
  emailAndPassword: {
    enabled: true,
    disableSignUp: true,
    minPasswordLength: 12,
  },
  session: {
    expiresIn: 60 * 60 * 24,
    updateAge: 60 * 60,
    cookieCache: { enabled: false },
  },
  rateLimit: {
    enabled: true,
    storage: "database",
    window: 60,
    max: 30,
    customRules: { "/sign-in/email": { window: 60, max: 5 } },
  },
  advanced: { useSecureCookies: process.env.NODE_ENV === "production" },
});
