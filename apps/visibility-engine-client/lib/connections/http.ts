import { lookup } from "node:dns/promises";
import { isIP, BlockList } from "node:net";
import https from "node:https";

// Errors are a closed vocabulary. Provider bodies, headers and transport errors
// are never interpolated into client-visible messages, audits or stored results.
export class ProviderError extends Error {
  constructor(
    public code: string,
    message: string,
    public retryable = false,
  ) {
    super(message);
  }
}
export function providerError(error: unknown) {
  return error instanceof ProviderError
    ? error
    : new ProviderError(
        "unavailable",
        "Provider unavailable. Check the connection and try again.",
        true,
      );
}
const blocked = new BlockList();
for (const [ip, bits] of [
  ["0.0.0.0", 8],
  ["10.0.0.0", 8],
  ["100.64.0.0", 10],
  ["127.0.0.0", 8],
  ["169.254.0.0", 16],
  ["168.63.129.16", 32],
  ["192.88.99.0", 24],
  ["172.16.0.0", 12],
  ["192.0.0.0", 24],
  ["192.0.2.0", 24],
  ["192.168.0.0", 16],
  ["198.18.0.0", 15],
  ["198.51.100.0", 24],
  ["203.0.113.0", 24],
  ["224.0.0.0", 4],
  ["240.0.0.0", 4],
] as const)
  blocked.addSubnet(ip, bits, "ipv4");
export function publicAddress(ip: string) {
  if (isIP(ip) === 4) return !blocked.check(ip, "ipv4");
  // Only global unicast IPv6; excludes mapped IPv4, NAT64, local, multicast,
  // documentation, Teredo and 6to4 transition destinations.
  if (isIP(ip) !== 6) return false;
  const list = new BlockList();
  list.addSubnet("2000::", 3, "ipv6");
  const deny = new BlockList();
  for (const [prefix, bits] of [
    ["2001::", 23],
    ["3fff::", 20],
    ["2001:db8::", 32],
    ["2002::", 16],
  ] as const)
    deny.addSubnet(prefix, bits, "ipv6");
  return list.check(ip, "ipv6") && !deny.check(ip, "ipv6");
}
export function destination(value: string) {
  let url: URL;
  try {
    url = new URL(value);
  } catch {
    throw new ProviderError(
      "destination",
      "Site URL blocked for security reasons.",
    );
  }
  const hostname = url.hostname
    .replace(/^\[|\]$/g, "")
    .toLowerCase()
    .replace(/\.$/, "");
  if (
    url.protocol !== "https:" ||
    url.username ||
    url.password ||
    url.hash ||
    hostname === "localhost" ||
    hostname.endsWith(".localhost") ||
    hostname.endsWith(".local") ||
    hostname === "metadata.google.internal" ||
    (isIP(hostname) && !publicAddress(hostname))
  )
    throw new ProviderError(
      "destination",
      "Use a public HTTPS site URL without credentials. Private destinations are blocked.",
    );
  url.hostname = hostname;
  return url;
}
export type HttpOptions = {
  method?: string;
  headers?: Record<string, string>;
  body?: string;
  origin?: string;
  deadline?: number;
};
export type HttpResult = {
  status: number;
  body: string;
  headers: Record<string, string>;
  url: string;
};
export type Transport = (
  url: string,
  options?: HttpOptions,
) => Promise<HttpResult>;
export type Resolver = (
  hostname: string,
) => Promise<{ address: string; family: number }[]>;
const resolve: Resolver = (hostname) =>
  lookup(hostname, { all: true, verbatim: true });
export async function checkedDestination(
  value: string,
  resolver: Resolver = resolve,
) {
  const url = destination(value);
  const host = url.hostname.replace(/^\[|\]$/g, "");
  let addresses;
  try {
    addresses = isIP(host)
      ? [{ address: host, family: isIP(host) }]
      : await new Promise<Awaited<ReturnType<Resolver>>>((done, fail) => {
          const timer = setTimeout(() => fail(new Error("DNS timeout")), 5000);
          resolver(host)
            .then(done, fail)
            .finally(() => clearTimeout(timer));
        });
  } catch {
    throw new ProviderError("dns", "Site DNS could not be resolved.", true);
  }
  if (!addresses.length || addresses.some((a) => !publicAddress(a.address)))
    throw new ProviderError(
      "destination",
      "Site URL blocked for security reasons.",
    );
  return { url, address: addresses[0] };
}
// DNS is checked once per hop and the exact validated address is pinned to the
// TLS connection. SNI and certificate verification still use the original host.
export function safeTransport(resolver: Resolver = resolve): Transport {
  return async (value, options = {}) => {
    let next = value;
    for (let hop = 0; hop < 5; hop++) {
      if (options.deadline && Date.now() >= options.deadline)
        throw new ProviderError(
          "timeout",
          "Provider operation timed out. A retry may be required.",
          true,
        );
      const { url, address } = await checkedDestination(next, resolver);
      if (options.origin && url.origin !== options.origin)
        throw new ProviderError(
          "redirect",
          "Provider redirected to another site. Reconnect using its canonical URL.",
        );
      const result = await new Promise<HttpResult>((done, fail) => {
        const request = https.request(
          url,
          {
            method: options.method || "GET",
            family: address.family,
            headers: { ...options.headers, host: url.host },
            lookup: (_hostname, _options, callback) =>
              callback(null, address.address, address.family),
            timeout: 20000,
            maxHeaderSize: 16384,
          },
          (response) => {
            const chunks: Buffer[] = [];
            let size = 0;
            response.on("data", (chunk) => {
              size += chunk.length;
              if (size > 4 * 1024 * 1024) {
                request.destroy();
                fail(
                  new ProviderError(
                    "size",
                    "Provider response exceeded the supported size.",
                  ),
                );
              } else chunks.push(chunk);
            });
            response.on("end", () =>
              done({
                status: response.statusCode || 0,
                body: Buffer.concat(chunks).toString("utf8"),
                headers: Object.fromEntries(
                  Object.entries(response.headers).map(([k, v]) => [
                    k,
                    Array.isArray(v) ? v.join(",") : v || "",
                  ]),
                ),
                url: url.href,
              }),
            );
            response.on("error", () =>
              fail(
                new ProviderError(
                  "network",
                  "Provider response was interrupted.",
                  true,
                ),
              ),
            );
          },
        );
        const timer = setTimeout(
          () => request.destroy(),
          Math.max(
            1,
            Math.min(
              20000,
              (options.deadline || Date.now() + 20000) - Date.now(),
            ),
          ),
        );
        timer.unref();
        request.on("close", () => clearTimeout(timer));
        request.on("timeout", () => request.destroy());
        request.on("error", () =>
          fail(
            new ProviderError(
              "network",
              "Provider could not be reached securely.",
              true,
            ),
          ),
        );
        request.end(options.body);
      });
      if (![301, 302, 303, 307, 308].includes(result.status)) return result;
      if (!result.headers.location)
        throw new ProviderError("redirect", "Invalid provider redirect.");
      const target = new URL(result.headers.location, url);
      // Never forward credentials cross-origin, and never replay a write after
      // a redirect (including a same-origin POST -> GET downgrade).
      await checkedDestination(target.href, resolver);
      if (
        target.origin !== url.origin ||
        (options.method &&
          options.method !== "GET" &&
          options.method !== "HEAD")
      )
        throw new ProviderError(
          "redirect",
          "Provider redirect blocked. Use the canonical HTTPS URL.",
        );
      next = target.href;
    }
    throw new ProviderError("redirect", "Too many provider redirects.");
  };
}
export const http = safeTransport();
export function json<T>(result: HttpResult): T {
  if (result.status === 401 || result.status === 403)
    throw new ProviderError(
      "authentication",
      "Authentication failed or account permissions are insufficient. Reconnect the account.",
    );
  if (result.status === 429)
    throw new ProviderError(
      "quota",
      "Provider quota or rate limit reached. A retry is scheduled.",
      true,
    );
  if (result.status >= 500)
    throw new ProviderError(
      "unavailable",
      "Provider temporarily unavailable.",
      true,
    );
  if (result.status < 200 || result.status >= 300)
    throw new ProviderError(
      "response",
      "Provider rejected the request. Check the connection and content.",
    );
  try {
    return JSON.parse(result.body) as T;
  } catch {
    throw new ProviderError(
      "response",
      "Provider returned an invalid response.",
    );
  }
}
