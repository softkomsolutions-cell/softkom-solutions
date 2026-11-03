// Test-runner-only transport routing. Never imported by application code.
// Production destination checks still execute; test sockets are then routed to
// the disposable WordPress server and explicit Google fixtures on loopback.
import https from "node:https";
import http from "node:http";
import dns from "node:dns/promises";
import { syncBuiltinESMExports } from "node:module";
const hosts = new Set([
  "wordpress.visibility.test",
  "www.googleapis.com",
  "oauth2.googleapis.com",
]);
const lookup = dns.lookup;
dns.lookup = async function (host, ...args) {
  if (hosts.has(host))
    return args[0]?.all
      ? [{ address: "93.184.216.34", family: 4 }]
      : { address: "93.184.216.34", family: 4 };
  return lookup.call(this, host, ...args);
};
const request = https.request;
https.request = function (url, options, callback) {
  const target = typeof url === "string" ? new URL(url) : url;
  if (target instanceof URL && hosts.has(target.hostname)) {
    const port = target.hostname === "wordpress.visibility.test" ? 8086 : 8899;
    const local = new URL(
      `http://127.0.0.1:${port}${target.pathname}${target.search}`,
    );
    return http.request(
      local,
      {
        ...options,
        lookup: undefined,
        headers: { ...options?.headers, host: target.host },
      },
      callback,
    );
  }
  return request.call(this, url, options, callback);
};
syncBuiltinESMExports();
