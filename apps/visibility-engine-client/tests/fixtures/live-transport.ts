import { readFileSync } from "node:fs";
import http from "node:http";
import type { Transport } from "../../lib/connections/http";
export function wordpressCredentials() {
  return JSON.parse(readFileSync(".test-services.json", "utf8")) as {
    siteUrl: string;
    username: string;
    password: string;
    subscriber: string;
    subscriberPassword: string;
  };
}
// Explicit controlled routing lives in tests only. Production code has no
// environment flag that skips private-address or TLS validation.
export const localProviderTransport: Transport = async (
  value,
  options = {},
) => {
  const target = new URL(value);
  const port =
    target.hostname === "wordpress.visibility.test"
      ? 8086
      : ["www.googleapis.com", "oauth2.googleapis.com"].includes(
            target.hostname,
          )
        ? 8899
        : null;
  if (!port) throw new Error("Unsupported controlled fixture destination.");
  return new Promise((resolve, reject) => {
    const request = http.request(
      {
        hostname: "127.0.0.1",
        port,
        path: target.pathname + target.search,
        method: options.method,
        headers: { ...options.headers, host: target.host },
      },
      (response) => {
        const chunks: Buffer[] = [];
        response.on("data", (chunk) => chunks.push(chunk));
        response.on("end", () =>
          resolve({
            status: response.statusCode || 0,
            headers: Object.fromEntries(
              Object.entries(response.headers).map(([key, v]) => [
                key,
                Array.isArray(v) ? v.join(",") : v || "",
              ]),
            ),
            body: Buffer.concat(chunks).toString(),
            url: value,
          }),
        );
      },
    );
    request.on("error", reject);
    request.end(options.body);
  });
};
