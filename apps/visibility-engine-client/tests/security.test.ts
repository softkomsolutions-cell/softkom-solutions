import { describe, expect, it } from "vitest";
import {
  assertApproved,
  canManage,
  cleanContent,
  safeReturnPath,
  webUrl,
} from "../lib/security";
import {
  encryptCredential,
  decryptCredential,
} from "../lib/connections/credentials";
import { searchTotals, monthRange } from "../lib/queries";
import { publishingProvider } from "../lib/publishing/providers";
describe("security boundaries", () => {
  it("never turns a client or ordinary owner into a Softkom override", () => {
    for (const role of ["CLIENT", "OWNER", "ADMIN"] as const)
      expect(() =>
        assertApproved(
          { status: "CLIENT_REVIEW", revision: 2, approvals: [] },
          role,
          "A sufficiently long override reason",
        ),
      ).toThrow();
    expect(() =>
      assertApproved(
        { status: "CLIENT_REVIEW", revision: 2, approvals: [] },
        "SOFTKOM_MANAGER",
        "A documented emergency override reason",
      ),
    ).not.toThrow();
    expect(canManage("CLIENT")).toBe(false);
  });
  it("rejects stale approvals and change-requested content", () => {
    expect(() =>
      assertApproved(
        {
          status: "APPROVED",
          revision: 2,
          approvals: [{ decision: "APPROVED", revision: 1 }],
        },
        "ADMIN",
      ),
    ).toThrow();
    expect(() =>
      assertApproved(
        {
          status: "CHANGES_REQUESTED",
          revision: 2,
          approvals: [{ decision: "APPROVED", revision: 2 }],
        },
        "ADMIN",
      ),
    ).toThrow();
    expect(() =>
      assertApproved(
        {
          status: "APPROVED",
          revision: 2,
          approvals: [{ decision: "APPROVED", revision: 2 }],
        },
        "ADMIN",
      ),
    ).not.toThrow();
  });
  it("removes scripts, event attributes, SVG and unsafe URL schemes", () => {
    const html = cleanContent(
      '<script>alert(1)</script><svg onload="evil()"></svg><p onclick="evil()">Useful <strong>copy</strong><a href="javascript:evil()">link</a><iframe src="https://evil.test"></iframe></p>',
    );
    expect(html).toContain("<strong>copy</strong>");
    expect(html).not.toMatch(/script|onclick|onload|javascript:|iframe|svg/);
  });
  it("allows only local workspace return paths and HTTP site URLs", () => {
    for (const path of [
      "//evil.test",
      "https://evil.test",
      "/\\evil.test",
      "/workspace/../../login",
      "/workspace?to=evil",
    ])
      expect(safeReturnPath(path)).toBe("/workspace/dashboard");
    expect(safeReturnPath("/workspace/content/abc")).toBe(
      "/workspace/content/abc",
    );
    for (const url of [
      "javascript:alert(1)",
      "file:///etc/passwd",
      "https://user:pass@example.com",
    ])
      expect(webUrl.safeParse(url).success).toBe(false);
  });
  it("encrypts credentials with authenticated random envelopes", () => {
    process.env.CONNECTION_ENCRYPTION_KEY = Buffer.alloc(32, 7).toString(
      "base64",
    );
    const first = encryptCredential("sensitive-value");
    const second = encryptCredential("sensitive-value");
    expect(first).not.toContain("sensitive-value");
    expect(first).not.toBe(second);
    expect(decryptCredential(first)).toBe("sensitive-value");
    const parts = first.split(".");
    parts[3] = Buffer.from("tamper").toString("base64");
    expect(() => decryptCredential(parts.join("."))).toThrow();
    delete process.env.CONNECTION_ENCRYPTION_KEY;
  });
  it("keeps unavailable providers explicitly unavailable", async () => {
    const provider = publishingProvider("WORDPRESS");
    expect(provider.available).toBe(false);
    await expect(
      provider.publish({
        title: "x",
        html: "x",
        slug: "x",
        metaTitle: "x",
        metaDescription: "x",
        idempotencyKey: "x",
      }),
    ).rejects.toThrow("not yet available");
  });
});
describe("measured reporting", () => {
  it("weights position and computes CTR from totals", () => {
    expect(
      searchTotals([
        { clicks: 5, impressions: 10, position: 2 },
        { clicks: 5, impressions: 90, position: 12 },
      ]),
    ).toEqual({ clicks: 10, impressions: 100, ctr: 0.1, position: 11 });
    expect(searchTotals([]).ctr).toBeNull();
    expect(searchTotals([]).position).toBeNull();
  });
  it("validates calendar ranges instead of silently rolling invalid months", () => {
    expect(() => monthRange("2026-13")).toThrow();
    expect(monthRange("2026-12").to.toISOString()).toBe(
      "2026-12-31T22:00:00.000Z",
    );
  });
});
