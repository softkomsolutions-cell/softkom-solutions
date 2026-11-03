import "dotenv/config";
import { processPublication } from "../lib/publishing/worker";
import { processSync, scheduleSyncs } from "../lib/analytics/sync";
import { db } from "../lib/db";
// Run once from a scheduler each minute; bounded batches let overlapping invocations
// cooperate through atomic database claims. Logs contain counts, never payloads.
try {
  const scheduled = await scheduleSyncs();
  let publications = 0,
    syncs = 0;
  for (let i = 0; i < 20; i++) {
    if (!(await processPublication())) break;
    publications++;
  }
  for (let i = 0; i < 10; i++) {
    if (!(await processSync())) break;
    syncs++;
  }
  console.log(
    JSON.stringify({
      scheduled,
      publications,
      syncs,
      finishedAt: new Date().toISOString(),
    }),
  );
} catch {
  console.error(
    "Worker run failed. Check database readiness and persisted job errors.",
  );
  process.exitCode = 1;
} finally {
  await db.$disconnect();
}
