-- AlterEnum
ALTER TYPE "ConnectionState" ADD VALUE 'CONNECTING';

-- AlterTable
ALTER TABLE "Organisation" ADD COLUMN     "brandedTerms" TEXT NOT NULL DEFAULT '';

-- AlterTable
ALTER TABLE "Publication" ADD COLUMN     "claimToken" TEXT,
ADD COLUMN     "jobKey" TEXT,
ADD COLUMN     "lastAttemptAt" TIMESTAMP(3),
ADD COLUMN     "leaseUntil" TIMESTAMP(3),
ADD COLUMN     "verificationError" TEXT,
ADD COLUMN     "verifiedAt" TIMESTAMP(3);

-- AlterTable
ALTER TABLE "SearchMetric" ADD COLUMN     "grainKey" TEXT;

-- AlterTable
ALTER TABLE "ContentRevision" ADD COLUMN     "funnel" TEXT,
ADD COLUMN     "slug" TEXT;

-- AlterTable
ALTER TABLE "SiteConnection" ADD COLUMN     "accountLabel" TEXT,
ADD COLUMN     "generation" INTEGER NOT NULL DEFAULT 0,
ADD COLUMN     "label" TEXT,
ADD COLUMN     "properties" JSONB,
ADD COLUMN     "selectedProperty" TEXT,
ADD COLUMN     "tokenExpiresAt" TIMESTAMP(3),
ADD COLUMN     "verifiedAt" TIMESTAMP(3);

-- CreateTable
CREATE TABLE "OAuthState" (
    "hash" TEXT NOT NULL,
    "userId" TEXT NOT NULL,
    "organisationId" TEXT NOT NULL,
    "siteId" TEXT NOT NULL,
    "verifier" TEXT NOT NULL,
    "expiresAt" TIMESTAMP(3) NOT NULL,
    "generation" INTEGER NOT NULL,

    CONSTRAINT "OAuthState_pkey" PRIMARY KEY ("hash")
);

-- CreateTable
CREATE TABLE "SearchSync" (
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
    "lastFrom" TIMESTAMP(3),
    "lastTo" TIMESTAMP(3),
    "latestDataDate" TIMESTAMP(3),
    "error" TEXT,
    "generation" INTEGER NOT NULL,
    "updatedAt" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "SearchSync_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "SearchSync_connectionId_key" ON "SearchSync"("connectionId");

-- CreateIndex
CREATE UNIQUE INDEX "Publication_jobKey_key" ON "Publication"("jobKey");

-- CreateIndex
CREATE UNIQUE INDEX "SearchMetric_grainKey_key" ON "SearchMetric"("grainKey");

-- AddForeignKey
ALTER TABLE "SearchSync" ADD CONSTRAINT "SearchSync_connectionId_fkey" FOREIGN KEY ("connectionId") REFERENCES "SiteConnection"("id") ON DELETE CASCADE ON UPDATE CASCADE;


-- Preserve existing snapshots; fill only newly added routing fields.
UPDATE "ContentRevision" r SET slug=c.slug, funnel=c.funnel FROM "ContentItem" c WHERE c.id=r."contentId";
-- Retain every historical publication; only the newest legacy job is used as
-- the deduplication boundary for its content revision.
WITH ranked AS (SELECT id, "contentId", "requestedRevision", row_number() OVER(PARTITION BY "contentId","requestedRevision" ORDER BY "createdAt" DESC,id) AS n FROM "Publication")
UPDATE "Publication" p SET "jobKey"=r."contentId" || ':' || r."requestedRevision" FROM ranked r WHERE p.id=r.id AND r.n=1;
