import { OrgRole } from "@prisma/client";
import { z } from "zod";
import sanitizeHtml from "sanitize-html";

export class AccessError extends Error {
  constructor(
    public status: 403 | 404 | 409,
    message: string,
  ) {
    super(message);
  }
}
export const idSchema = z
  .string()
  .min(1)
  .max(128)
  .regex(/^[a-zA-Z0-9_-]+$/);
export const text = (max = 300) => z.string().trim().min(1).max(max);
export const webUrl = z
  .string()
  .url()
  .max(2048)
  .refine((value) => {
    const url = new URL(value);
    return (
      ["https:", "http:"].includes(url.protocol) &&
      !url.username &&
      !url.password
    );
  }, "Use an HTTP or HTTPS URL without credentials.");
export function canManage(role: OrgRole) {
  return ["OWNER", "ADMIN", "SOFTKOM_MANAGER"].includes(role);
}
export function requireManager(role: OrgRole) {
  if (!canManage(role))
    throw new AccessError(
      403,
      "Your role can review content. A manager must make this change.",
    );
}
export function safeReturnPath(value: unknown) {
  return typeof value === "string" &&
    /^\/workspace(?:\/[a-zA-Z0-9_/-]*)?$/.test(value)
    ? value
    : "/workspace/dashboard";
}
export function cleanContent(value: string) {
  return sanitizeHtml(value, {
    allowedTags: [
      "p",
      "br",
      "h2",
      "h3",
      "h4",
      "ul",
      "ol",
      "li",
      "strong",
      "em",
      "a",
      "blockquote",
      "table",
      "thead",
      "tbody",
      "tr",
      "td",
      "th",
    ],
    allowedAttributes: { a: ["href", "title"] },
    allowedSchemes: ["https", "http", "mailto"],
    allowProtocolRelative: false,
  });
}
export function assertApproved(
  input: {
    status: string;
    revision: number;
    approvals: { decision: string; revision: number }[];
  },
  role: OrgRole,
  override?: string,
) {
  if (override) {
    if (role !== "SOFTKOM_MANAGER" || override.trim().length < 20)
      throw new AccessError(
        403,
        "A Softkom manager and an explicit reason of at least 20 characters are required.",
      );
    return;
  }
  if (
    input.status !== "APPROVED" ||
    !input.approvals.some(
      (a) => a.decision === "APPROVED" && a.revision === input.revision,
    )
  ) {
    throw new AccessError(
      409,
      "This revision needs client approval before publication.",
    );
  }
}
