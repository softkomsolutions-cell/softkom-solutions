import { NextResponse } from "next/server";
import { toNextJsHandler } from "better-auth/next-js";
import { auth } from "@/lib/auth";

const handler = toNextJsHandler(auth);

function trustedRequestOrigin(request: Request) {
  const configured = process.env.BETTER_AUTH_URL;
  if (!configured) return process.env.NODE_ENV !== "production";

  const allowed = new URL(configured).origin;
  const origin = request.headers.get("origin");
  if (origin && origin !== allowed) return false;

  const referer = request.headers.get("referer");
  if (!origin && referer) {
    try {
      if (new URL(referer).origin !== allowed) return false;
    } catch {
      return false;
    }
  }
  return true;
}

export const GET = handler.GET;

export async function POST(request: Request) {
  if (!trustedRequestOrigin(request)) {
    return NextResponse.json({ error: "Untrusted request origin" }, { status: 403 });
  }
  return handler.POST(request);
}
