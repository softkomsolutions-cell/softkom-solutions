import { describe, expect, it } from "vitest";
import { payfastConfig, payfastSignature, subscriptionFields } from "../lib/payfast";

describe("PayFast checkout safety", () => {
  it("defaults to sandbox and refuses checkout without credentials", () => {
    const previous = {
      id: process.env.PAYFAST_MERCHANT_ID,
      key: process.env.PAYFAST_MERCHANT_KEY,
      phrase: process.env.PAYFAST_PASSPHRASE,
      url: process.env.NEXT_PUBLIC_APP_URL,
      sandbox: process.env.PAYFAST_SANDBOX,
    };
    try {
      delete process.env.PAYFAST_MERCHANT_ID;
      expect(payfastConfig()).toBeNull();
      process.env.PAYFAST_MERCHANT_ID = "10000100";
      process.env.PAYFAST_MERCHANT_KEY = "testkey";
      process.env.PAYFAST_PASSPHRASE = "testphrase";
      process.env.NEXT_PUBLIC_APP_URL = "https://visibility.example";
      delete process.env.PAYFAST_SANDBOX;
      expect(payfastConfig()?.sandbox).toBe(true);
      expect(payfastConfig()?.processUrl).toContain("sandbox.payfast.co.za");
      process.env.NEXT_PUBLIC_APP_URL = "http://visibility.example";
      expect(payfastConfig()).toBeNull();
    } finally {
      for (const [key, value] of Object.entries({
        PAYFAST_MERCHANT_ID: previous.id,
        PAYFAST_MERCHANT_KEY: previous.key,
        PAYFAST_PASSPHRASE: previous.phrase,
        NEXT_PUBLIC_APP_URL: previous.url,
        PAYFAST_SANDBOX: previous.sandbox,
      })) {
        if (value === undefined) delete process.env[key];
        else process.env[key] = value;
      }
    }
  });

  it("signs checkout fields and prevents signature reuse after price tampering", () => {
    const fields = subscriptionFields({
      merchantId: "10000100", merchantKey: "testkey",
      origin: "https://visibility.example", email: "buyer@example.com",
      paymentId: "payment-test", plan: "STARTER",
    });
    expect(fields.amount).toBe("1490.00");
    expect(fields.recurring_amount).toBe("1490.00");
    expect(fields.subscription_type).toBe("1");
    expect(fields.notify_url).toBe("https://visibility.example/api/payfast/itn");
    const signature = payfastSignature(fields, "secret");
    expect(signature).toMatch(/^[a-f0-9]{32}$/);
    expect(payfastSignature({ ...fields, amount: "1.00" }, "secret")).not.toBe(signature);
  });
});
