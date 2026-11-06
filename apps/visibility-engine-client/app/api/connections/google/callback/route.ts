import { getContext } from "@/lib/context";
import { finishGoogle } from "@/lib/analytics/service";
import { AccessError } from "@/lib/security";
import { NextResponse } from "next/server";
export const runtime = "nodejs";
export async function GET(request: Request) {
  const ctx = await getContext();
  const url = new URL(request.url);
  try {
    await finishGoogle(
      ctx,
      url.searchParams.get("state") || "",
      url.searchParams.get("code"),
    );
  } catch (error) {
    if (error instanceof AccessError && error.status === 403)
      return NextResponse.json(
        {
          error:
            "Google authorisation state is invalid or expired. Start again from Connections.",
        },
        { status: 403 },
      );
  }
  return NextResponse.redirect(
    new URL("/workspace/connections", process.env.BETTER_AUTH_URL),
    303,
  );
}
