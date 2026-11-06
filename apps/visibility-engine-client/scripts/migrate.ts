import "dotenv/config";
import { readFile, readdir } from "node:fs/promises";
import { createHash, randomUUID } from "node:crypto";
import { resolve } from "node:path";
import pg from "pg";

// Apply Prisma-generated SQL with PostgreSQL directly. Prisma 6's experimental
// WASM migration adapter cannot decode PostgreSQL's catalog `name` type. Keep a
// standard Prisma ledger, so native-engine Prisma tooling remains compatible.
const client = new pg.Client({ connectionString: process.env.DATABASE_URL });
await client.connect();
try {
  await client.query("SELECT pg_advisory_lock(738194026)");
  await client.query(`CREATE TABLE IF NOT EXISTS "_prisma_migrations" (
    "id" VARCHAR(36) PRIMARY KEY, "checksum" VARCHAR(64) NOT NULL,
    "finished_at" TIMESTAMPTZ, "migration_name" VARCHAR(255) NOT NULL,
    "logs" TEXT, "rolled_back_at" TIMESTAMPTZ,
    "started_at" TIMESTAMPTZ NOT NULL DEFAULT now(), "applied_steps_count" INTEGER NOT NULL DEFAULT 0
  )`);
  const { rows: applied } = await client.query<{
    migration_name: string;
    checksum: string;
    finished_at: Date | null;
    rolled_back_at: Date | null;
  }>(
    'SELECT migration_name, checksum, finished_at, rolled_back_at FROM "_prisma_migrations" ORDER BY started_at',
  );
  if (applied.some((m) => !m.finished_at && !m.rolled_back_at))
    throw new Error(
      "An unfinished migration requires operator investigation before continuing.",
    );
  if (!applied.some((m) => m.finished_at)) {
    const { rows } = await client.query<{ exists: boolean }>(
      `SELECT to_regclass('public."Organisation"') IS NOT NULL AS exists`,
    );
    if (rows[0].exists)
      throw new Error(
        "Existing schema detected. Verify and baseline the original schema with Prisma migrate resolve before deploying additive migrations.",
      );
  }
  const root = resolve("prisma/migrations");
  const migrations = (await readdir(root, { withFileTypes: true }))
    .filter((d) => d.isDirectory())
    .map((d) => d.name)
    .sort();
  if (
    applied.some(
      (m) =>
        m.finished_at &&
        !m.rolled_back_at &&
        !migrations.includes(m.migration_name),
    )
  ) {
    throw new Error(
      "The database contains a migration missing from this checkout. Refusing an older or incomplete migration set.",
    );
  }
  for (const name of migrations) {
    const sql = await readFile(resolve(root, name, "migration.sql"), "utf8");
    const checksum = createHash("sha256").update(sql).digest("hex");
    const previous = applied.find(
      (m) => m.migration_name === name && m.finished_at && !m.rolled_back_at,
    );
    if (previous) {
      if (previous.checksum !== checksum)
        throw new Error(`Applied migration checksum changed: ${name}`);
      continue;
    }
    await client.query("BEGIN");
    try {
      await client.query(sql);
      await client.query(
        'INSERT INTO "_prisma_migrations" (id,checksum,migration_name,finished_at,applied_steps_count) VALUES ($1,$2,$3,now(),1)',
        [randomUUID(), checksum, name],
      );
      await client.query("COMMIT");
      console.log(`Applied ${name}`);
    } catch (error) {
      await client.query("ROLLBACK");
      throw error;
    }
  }
  console.log("All migrations are applied; checksums verified.");
} finally {
  await client.query("SELECT pg_advisory_unlock(738194026)").catch(() => {});
  await client.end();
}
