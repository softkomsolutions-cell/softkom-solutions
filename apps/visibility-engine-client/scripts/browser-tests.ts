import "dotenv/config";
import { execFileSync, spawnSync } from "node:child_process";
import { mkdirSync, existsSync, readFileSync, writeFileSync } from "node:fs";
import { createHash } from "node:crypto";
import { resolve } from "node:path";
mkdirSync(".test-tls", { recursive: true, mode: 0o700 });
execFileSync(
  "openssl",
  [
    "req",
    "-x509",
    "-newkey",
    "rsa:2048",
    "-nodes",
    "-keyout",
    ".test-tls/key.pem",
    "-out",
    ".test-tls/cert.pem",
    "-days",
    "2",
    "-subj",
    "/CN=localhost",
    "-addext",
    "subjectAltName=DNS:localhost,IP:127.0.0.1",
  ],
  { stdio: "ignore" },
);
const pubkey = execFileSync("openssl", [
  "x509",
  "-in",
  ".test-tls/cert.pem",
  "-pubkey",
  "-noout",
]);
const der = execFileSync("openssl", ["pkey", "-pubin", "-outform", "DER"], {
  input: pubkey,
});
writeFileSync(
  ".test-tls/spki",
  createHash("sha256").update(der).digest("base64"),
);
const system =
  process.env.NODE_EXTRA_CA_CERTS && existsSync(process.env.NODE_EXTRA_CA_CERTS)
    ? readFileSync(process.env.NODE_EXTRA_CA_CERTS)
    : Buffer.from("");
writeFileSync(
  ".test-tls/ca-bundle.pem",
  Buffer.concat([
    system,
    Buffer.from("\n"),
    readFileSync(".test-tls/cert.pem"),
  ]),
);
const result = spawnSync(
  process.execPath,
  ["node_modules/@playwright/test/cli.js", "test", ...process.argv.slice(2)],
  {
    stdio: "inherit",
    env: {
      ...process.env,
      BETTER_AUTH_URL: "https://localhost:3443",
      NODE_EXTRA_CA_CERTS: resolve(".test-tls/ca-bundle.pem"),
    },
  },
);
process.exitCode = result.status ?? 1;
