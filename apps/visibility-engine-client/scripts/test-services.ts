import { execFileSync } from "node:child_process";
import { writeFileSync } from "node:fs";
import { randomBytes } from "node:crypto";
// Creates only named disposable test services. No production connection is used.
const docker = (...args: string[]) =>
  execFileSync("docker", args, {
    encoding: "utf8",
    stdio: ["ignore", "pipe", "pipe"],
  }).trim();
const prefix = "visibility-phase2-wordpress";
const wpImage =
  "wordpress@sha256:21c84c09b4a9a1dbb24eabb5a08661e1fbfd1e399342a1c1e9801e15ea7a4fa2";
const cliImage =
  "wordpress@sha256:a8eb972af44dd9600db6e6511b66a0c8d5300eb42bf8d3a2f7d2bbb0a95133fb";
const dbImage =
  "mariadb@sha256:d1891ede5a559f1086f55053bb8cc1569b0298338c4d72d71725a7f74f6ba620";
if (process.argv.includes("--stop")) {
  for (const name of [prefix, `${prefix}-db`])
    try {
      docker("rm", "-f", name);
    } catch {}
  try {
    docker("volume", "rm", `${prefix}-core`);
  } catch {}
  try {
    docker("network", "rm", prefix);
  } catch {}
  process.exit(0);
}
try {
  for (const image of [wpImage, cliImage, dbImage]) docker("pull", image);
  try {
    docker("network", "create", prefix);
  } catch {}
  try {
    docker("volume", "create", `${prefix}-core`);
  } catch {}
  for (const name of [prefix, `${prefix}-db`])
    try {
      docker("rm", "-f", name);
    } catch {}
  const databasePassword = randomBytes(24).toString("hex");
  docker(
    "run",
    "-d",
    "--name",
    `${prefix}-db`,
    "--network",
    prefix,
    "--network-alias",
    "db",
    "-e",
    "MARIADB_DATABASE=wordpress",
    "-e",
    "MARIADB_USER=wordpress",
    "-e",
    `MARIADB_PASSWORD=${databasePassword}`,
    "-e",
    `MARIADB_ROOT_PASSWORD=${databasePassword}`,
    dbImage,
  );
  const config =
    "define('WP_HOME','https://wordpress.visibility.test'); define('WP_SITEURL','https://wordpress.visibility.test'); define('WP_ENVIRONMENT_TYPE','local'); define('DISABLE_WP_CRON',true); define('AUTOMATIC_UPDATER_DISABLED',true);";
  docker(
    "run",
    "-d",
    "--name",
    prefix,
    "--network",
    prefix,
    "-p",
    "127.0.0.1:8086:80",
    "-v",
    `${prefix}-core:/var/www/html`,
    "-e",
    "WORDPRESS_DB_HOST=db",
    "-e",
    "WORDPRESS_DB_NAME=wordpress",
    "-e",
    "WORDPRESS_DB_USER=wordpress",
    "-e",
    `WORDPRESS_DB_PASSWORD=${databasePassword}`,
    "-e",
    `WORDPRESS_CONFIG_EXTRA=${config}`,
    wpImage,
  );
  const cli = (...args: string[]) =>
    docker(
      "run",
      "--rm",
      "--user",
      "33:33",
      "--network",
      prefix,
      "-v",
      `${prefix}-core:/var/www/html`,
      "-e",
      "WORDPRESS_DB_HOST=db",
      "-e",
      "WORDPRESS_DB_NAME=wordpress",
      "-e",
      "WORDPRESS_DB_USER=wordpress",
      "-e",
      `WORDPRESS_DB_PASSWORD=${databasePassword}`,
      "-e",
      `WORDPRESS_CONFIG_EXTRA=${config}`,
      cliImage,
      "wp",
      ...args,
    );
  let ready = false;
  for (let i = 0; i < 30; i++) {
    try {
      cli("core", "version");
      docker(
        "exec",
        `${prefix}-db`,
        "healthcheck.sh",
        "--connect",
        "--innodb_initialized",
      );
      ready = true;
      break;
    } catch {
      await new Promise((resolve) => setTimeout(resolve, 1000));
    }
  }
  if (!ready) throw new Error("Test services did not become ready.");
  const adminPassword = randomBytes(24).toString("hex");
  try {
    cli("core", "is-installed");
  } catch {
    cli(
      "core",
      "install",
      "--url=https://wordpress.visibility.test",
      "--title=Visibility controlled WordPress test",
      "--admin_user=visibility-fixture",
      `--admin_password=${adminPassword}`,
      "--admin_email=fixture@example.test",
      "--skip-email",
    );
  }
  cli("rewrite", "structure", "/%postname%/", "--hard");
  const password = cli(
    "user",
    "application-password",
    "create",
    "visibility-fixture",
    `Phase2 ${Date.now()}`,
    "--porcelain",
  );
  try {
    cli(
      "user",
      "create",
      "visibility-subscriber",
      "subscriber@example.test",
      "--role=subscriber",
      `--user_pass=${randomBytes(24).toString("hex")}`,
    );
  } catch {}
  const subscriberPassword = cli(
    "user",
    "application-password",
    "create",
    "visibility-subscriber",
    `Phase2 ${Date.now()}`,
    "--porcelain",
  );
  writeFileSync(
    ".test-services.json",
    JSON.stringify({
      siteUrl: "https://wordpress.visibility.test",
      username: "visibility-fixture",
      password,
      subscriber: "visibility-subscriber",
      subscriberPassword,
    }),
    { mode: 0o600 },
  );
  console.log(
    "Disposable WordPress ready; generated test credentials stored privately.",
  );
} catch {
  console.error(
    "Disposable WordPress setup failed. Inspect named test-service health; no credentials are logged.",
  );
  process.exitCode = 1;
}
