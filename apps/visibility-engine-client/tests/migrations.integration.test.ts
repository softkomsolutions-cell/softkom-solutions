import { afterAll, beforeAll, describe, expect, it } from "vitest";
import pg from "pg";
import { randomUUID, createHash } from "node:crypto";
import {
  readFile,
  mkdtemp,
  mkdir,
  cp,
  appendFile,
  writeFile,
  rm,
} from "node:fs/promises";
import { tmpdir } from "node:os";
import { resolve, join } from "node:path";
import { execFile } from "node:child_process";
import { promisify } from "node:util";
const exec = promisify(execFile);
const name = `visibility_migration_${randomUUID().replaceAll("-", "")}`;
const source = process.env.DATABASE_URL!;
const url = new URL(source);
url.pathname = `/${name}`;
const admin = new pg.Client({ connectionString: source });
let client: pg.Client;
let temporary: string;
const app = resolve(".");
const runner = () =>
  exec(
    process.execPath,
    [
      join(app, "node_modules/tsx/dist/cli.mjs"),
      join(app, "scripts/migrate.ts"),
    ],
    { cwd: temporary, env: { ...process.env, DATABASE_URL: url.toString() } },
  );
beforeAll(async () => {
  await admin.connect();
  await admin.query(`CREATE DATABASE "${name}"`);
  client = new pg.Client({ connectionString: url.toString() });
  await client.connect();
  temporary = await mkdtemp(join(tmpdir(), "visibility-migrations-"));
  await mkdir(join(temporary, "prisma"), { recursive: true });
  await cp(
    join(app, "prisma/migrations"),
    join(temporary, "prisma/migrations"),
    { recursive: true },
  );
  const baseline = await readFile(
    join(app, "prisma/migrations/202611030001_baseline/migration.sql"),
    "utf8",
  );
  await client.query(baseline);
  await client.query(`INSERT INTO "Organisation" (id,name,slug,"updatedAt") VALUES ('legacy-org','Legacy business','legacy',now());
    INSERT INTO "Site" (id,"organisationId",name,"baseUrl","updatedAt") VALUES ('legacy-site','legacy-org','Legacy site','https://example.test',now());
    INSERT INTO "ContentItem" (id,"siteId",title,slug,body,"metaTitle","updatedAt") VALUES ('legacy-content','legacy-site','Existing client copy','existing','<p>Preserve this original content.</p>','Original SEO title',now());
    CREATE TABLE "_prisma_migrations" (id VARCHAR(36) PRIMARY KEY, checksum VARCHAR(64) NOT NULL,finished_at TIMESTAMPTZ,migration_name VARCHAR(255) NOT NULL,logs TEXT,rolled_back_at TIMESTAMPTZ,started_at TIMESTAMPTZ NOT NULL DEFAULT now(),applied_steps_count INTEGER NOT NULL DEFAULT 0);`);
  await client.query(
    'INSERT INTO "_prisma_migrations" (id,checksum,migration_name,finished_at,applied_steps_count) VALUES ($1,$2,$3,now(),1)',
    [
      randomUUID(),
      createHash("sha256").update(baseline).digest("hex"),
      "202611030001_baseline",
    ],
  );
});
afterAll(async () => {
  await client?.end();
  await admin.query(`DROP DATABASE IF EXISTS "${name}" WITH (FORCE)`);
  await admin.end();
  if (temporary) await rm(temporary, { recursive: true, force: true });
});
describe("additive migration safety on an existing database", () => {
  it("preserves existing content, snapshots revisions, and repeats without changes", async () => {
    await runner();
    const { rows } = await client.query(
      'SELECT c.title,c.body,r.body AS snapshot FROM "ContentItem" c JOIN "ContentRevision" r ON r."contentId"=c.id WHERE c.id=$1',
      ["legacy-content"],
    );
    expect(rows).toEqual([
      {
        title: "Existing client copy",
        body: "<p>Preserve this original content.</p>",
        snapshot: "<p>Preserve this original content.</p>",
      },
    ]);
    const connections = await client.query(
      'SELECT status FROM "SiteConnection" WHERE "siteId"=$1',
      ["legacy-site"],
    );
    expect(connections.rows).toHaveLength(3);
    expect(connections.rows.every((r) => r.status !== "CONNECTED")).toBe(true);
    const repeat = await runner();
    expect(repeat.stdout).not.toContain("Applied 2026");
  });
  it("rejects changed checksums and rolls back a partial failing migration", async () => {
    const path = join(
      temporary,
      "prisma/migrations/202611030003_legacy_revision_snapshots/migration.sql",
    );
    await appendFile(path, "\n-- unintended edit\n");
    await expect(runner()).rejects.toThrow();
    await cp(
      join(
        app,
        "prisma/migrations/202611030003_legacy_revision_snapshots/migration.sql",
      ),
      path,
    );
    const failing = join(temporary, "prisma/migrations/209901010001_failure");
    await mkdir(failing);
    await writeFile(
      join(failing, "migration.sql"),
      'CREATE TABLE "MustRollback" (id text); SELECT 1 / 0;',
    );
    await expect(runner()).rejects.toThrow();
    const { rows } = await client.query(
      `SELECT to_regclass('public."MustRollback"') IS NULL AS rolled_back`,
    );
    expect(rows[0].rolled_back).toBe(true);
    expect(
      (
        await client.query(
          `SELECT count(*)::int AS count FROM "_prisma_migrations" WHERE migration_name='209901010001_failure'`,
        )
      ).rows[0].count,
    ).toBe(0);
  });
});
