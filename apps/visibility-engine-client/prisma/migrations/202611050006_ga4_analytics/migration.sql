-- GA4 commercial analytics metrics and independent sync state.
CREATE TABLE "AnalyticsMetric" (
  "id" TEXT NOT NULL,
  "siteId" TEXT NOT NULL,
  "date" TIMESTAMP(3) NOT NULL,
  "property" TEXT NOT NULL,
  "landingPage" TEXT NOT NULL,
  "source" TEXT NOT NULL,
  "medium" TEXT NOT NULL,
  "sessions" INTEGER NOT NULL DEFAULT 0,
  "engagedSessions" INTEGER NOT NULL DEFAULT 0,
  "conversions" DOUBLE PRECISION NOT NULL DEFAULT 0,
  "totalRevenue" DECIMAL(14,2) NOT NULL DEFAULT 0,
  "grainKey" TEXT NOT NULL,
  "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updatedAt" TIMESTAMP(3) NOT NULL,
  CONSTRAINT "AnalyticsMetric_pkey" PRIMARY KEY ("id")
);

CREATE TABLE "AnalyticsSync" (
  "id" TEXT NOT NULL,
  "connectionId" TEXT NOT NULL,
  "status" "PublishStatus" NOT NULL DEFAULT 'PENDING',
  "from" TIMESTAMP(3) NOT NULL,
  "to" TIMESTAMP(3) NOT NULL,
  "attempts" INTEGER NOT NULL DEFAULT 0,
  "claimToken" TEXT,
  "leaseUntil" TIMESTAMP(3),
  "nextRetryAt" TIMESTAMP(3),
  "lastAttemptAt" TIMESTAMP(3),
  "lastSuccessAt" TIMESTAMP(3),
  "latestDataDate" TIMESTAMP(3),
  "error" TEXT,
  "generation" INTEGER NOT NULL,
  "updatedAt" TIMESTAMP(3) NOT NULL,
  CONSTRAINT "AnalyticsSync_pkey" PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "AnalyticsMetric_grainKey_key" ON "AnalyticsMetric"("grainKey");
CREATE INDEX "AnalyticsMetric_siteId_date_idx" ON "AnalyticsMetric"("siteId", "date");
CREATE INDEX "AnalyticsMetric_siteId_property_date_idx" ON "AnalyticsMetric"("siteId", "property", "date");
CREATE UNIQUE INDEX "AnalyticsSync_connectionId_key" ON "AnalyticsSync"("connectionId");

ALTER TABLE "AnalyticsMetric"
  ADD CONSTRAINT "AnalyticsMetric_siteId_fkey"
  FOREIGN KEY ("siteId") REFERENCES "Site"("id") ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE "AnalyticsSync"
  ADD CONSTRAINT "AnalyticsSync_connectionId_fkey"
  FOREIGN KEY ("connectionId") REFERENCES "SiteConnection"("id") ON DELETE CASCADE ON UPDATE CASCADE;
