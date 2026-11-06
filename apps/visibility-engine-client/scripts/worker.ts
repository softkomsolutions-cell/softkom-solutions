import "dotenv/config";
import { processPublication } from "../lib/publishing/worker";
import { processSync, scheduleSyncs } from "../lib/analytics/sync";
import {
  processAnalyticsSync,
  scheduleAnalyticsSyncs,
} from "../lib/analytics/ga4-sync";
import { db } from "../lib/db";
import { monitorAIVisibility } from "../lib/ai/monitor";
import { generateEvidenceOpportunities } from "../lib/intelligence/opportunities";
// Run once from a scheduler each minute; bounded batches let overlapping invocations
// cooperate through atomic database claims. Logs contain counts, never payloads.
try {
  const scheduled = await scheduleSyncs();
  const analyticsScheduled = await scheduleAnalyticsSyncs();
  const aiVisibility = await monitorAIVisibility();
  const opportunitiesGenerated = await generateEvidenceOpportunities();
  let publications = 0,
    syncs = 0,
    analyticsSyncs = 0;
  for (let i = 0; i < 20; i++) {
    if (!(await processPublication())) break;
    publications++;
  }
  for (let i = 0; i < 10; i++) {
    if (!(await processSync())) break;
    syncs++;
  }
  for (let i = 0; i < 10; i++) {
    if (!(await processAnalyticsSync())) break;
    analyticsSyncs++;
  }
  console.log(
    JSON.stringify({
      scheduled,
      analyticsScheduled,
      aiVisibility,
      opportunitiesGenerated,
      publications,
      syncs,
      analyticsSyncs,
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
