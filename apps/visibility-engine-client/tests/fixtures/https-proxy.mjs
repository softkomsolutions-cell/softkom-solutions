import https from "node:https";
import http from "node:http";
import { readFileSync } from "node:fs";
// Fixture-only TLS terminator. Forwarding headers are overwritten, not trusted.
const server = https.createServer(
  {
    key: readFileSync(".test-tls/key.pem"),
    cert: readFileSync(".test-tls/cert.pem"),
  },
  (incoming, outgoing) => {
    const request = http.request(
      {
        host: "127.0.0.1",
        port: 3000,
        path: incoming.url,
        method: incoming.method,
        headers: {
          ...incoming.headers,
          host: "localhost:3443",
          "x-forwarded-host": "localhost:3443",
          "x-forwarded-proto": "https",
          "x-forwarded-for": "127.0.0.1",
        },
      },
      (response) => {
        outgoing.writeHead(response.statusCode, response.headers);
        response.pipe(outgoing);
      },
    );
    request.on("error", () => {
      outgoing.writeHead(503);
      outgoing.end("Test app starting");
    });
    incoming.pipe(request);
  },
);
server.listen(3443, "127.0.0.1");
process.on("SIGTERM", () => server.close());
