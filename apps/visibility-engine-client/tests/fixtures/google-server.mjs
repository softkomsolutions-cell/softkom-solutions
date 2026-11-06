// Deterministic OAuth/Search Console transport fixture, never production data.
import http from "node:http";
import { createHash } from "node:crypto";
let challenge = "",
  metrics = [];
const server = http.createServer(async (request, response) => {
  const chunks = [];
  for await (const chunk of request) chunks.push(chunk);
  const raw = Buffer.concat(chunks).toString();
  const path = new URL(request.url, "http://localhost").pathname;
  const send = (body, status = 200) => {
    response.writeHead(status, { "content-type": "application/json" });
    response.end(JSON.stringify(body));
  };
  if (path === "/health") return send({ fixture: true });
  if (path === "/__control/consent") {
    challenge = JSON.parse(raw).challenge;
    return send({ ok: true });
  }
  if (path === "/__control/metrics") {
    metrics = JSON.parse(raw);
    return send({ ok: true });
  }
  if (path === "/token") {
    const body = new URLSearchParams(raw);
    if (
      body.get("grant_type") === "authorization_code" &&
      createHash("sha256")
        .update(body.get("code_verifier") || "")
        .digest("base64url") !== challenge
    )
      return send({ error: "invalid_grant" }, 400);
    return send({
      access_token: "controlled-access-token",
      refresh_token: "controlled-refresh-token",
      expires_in: 3600,
    });
  }
  if (path === "/revoke") return send({ ok: true });
  if (path === "/webmasters/v3/sites")
    return send({
      siteEntry: [
        {
          siteUrl: "https://wordpress.visibility.test/",
          permissionLevel: "siteOwner",
        },
        {
          siteUrl: "sc-domain:wordpress.visibility.test",
          permissionLevel: "siteOwner",
        },
      ],
    });
  if (path.endsWith("/searchAnalytics/query")) {
    const range = JSON.parse(raw);
    return send({
      rows: metrics.filter(
        (r) => r.keys[0] >= range.startDate && r.keys[0] <= range.endDate,
      ),
    });
  }
  return send({ error: "unknown_fixture_request" }, 404);
});
server.listen(8899, "127.0.0.1");
process.on("SIGTERM", () => server.close());
