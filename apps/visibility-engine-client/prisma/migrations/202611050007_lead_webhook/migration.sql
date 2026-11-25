ALTER TYPE "DataOrigin" ADD VALUE IF NOT EXISTS 'WEBHOOK';

CREATE UNIQUE INDEX IF NOT EXISTS "LeadAttribution_siteId_leadId_key"
  ON "LeadAttribution"("siteId", "leadId");
