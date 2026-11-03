-- Retain existing observations without claiming a previously unknown property.
ALTER TABLE "SearchMetric" ADD COLUMN "property" TEXT;
CREATE INDEX "SearchMetric_siteId_property_date_idx" ON "SearchMetric"("siteId", "property", "date");
