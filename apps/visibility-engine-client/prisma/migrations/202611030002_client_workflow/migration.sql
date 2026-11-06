-- CreateEnum
CREATE TYPE "ConnectionProvider" AS ENUM ('WORDPRESS', 'GOOGLE_SEARCH_CONSOLE', 'GOOGLE_ANALYTICS_4');

-- CreateEnum
CREATE TYPE "ConnectionState" AS ENUM ('NOT_CONNECTED', 'COMING_SOON', 'REQUIRES_SETUP', 'CONNECTED', 'ERROR');

-- CreateEnum
CREATE TYPE "LeadStage" AS ENUM ('LEAD', 'QUALIFIED', 'OPPORTUNITY', 'WON', 'LOST');

-- CreateEnum
CREATE TYPE "DataOrigin" AS ENUM ('MANUAL', 'IMPORTED', 'CRM');

-- DropForeignKey
ALTER TABLE "ContentItem" DROP CONSTRAINT "ContentItem_topicId_fkey";

-- DropForeignKey
ALTER TABLE "Publication" DROP CONSTRAINT "Publication_contentId_fkey";

-- AlterTable
ALTER TABLE "User" ADD COLUMN     "emailVerified" BOOLEAN NOT NULL DEFAULT false,
ADD COLUMN     "image" TEXT;

-- AlterTable
ALTER TABLE "Topic" ADD COLUMN     "businessProblem" TEXT NOT NULL DEFAULT '',
ADD COLUMN     "contentType" TEXT NOT NULL DEFAULT 'Commercial problem page',
ADD COLUMN     "evidence" TEXT,
ADD COLUMN     "reasoning" TEXT NOT NULL DEFAULT '';

-- AlterTable
ALTER TABLE "ContentItem" ADD COLUMN     "contentType" TEXT NOT NULL DEFAULT 'Commercial problem page',
ADD COLUMN     "funnel" TEXT,
ADD COLUMN     "internalLinks" TEXT NOT NULL DEFAULT '',
ADD COLUMN     "publishingDestination" TEXT NOT NULL DEFAULT 'WordPress draft',
ADD COLUMN     "reviewerId" TEXT,
ADD COLUMN     "revision" INTEGER NOT NULL DEFAULT 1;

-- AlterTable
ALTER TABLE "Approval" ADD COLUMN     "revision" INTEGER NOT NULL DEFAULT 1;

-- AlterTable
ALTER TABLE "Publication" ADD COLUMN     "attempts" INTEGER NOT NULL DEFAULT 0,
ADD COLUMN     "nextRetryAt" TIMESTAMP(3),
ADD COLUMN     "overrideReason" TEXT,
ADD COLUMN     "providerResponse" JSONB,
ADD COLUMN     "requestedBy" TEXT,
ADD COLUMN     "requestedRevision" INTEGER NOT NULL DEFAULT 1;

-- AlterTable
ALTER TABLE "LeadAttribution" ADD COLUMN     "cluster" TEXT,
ADD COLUMN     "contentId" TEXT,
ADD COLUMN     "currency" TEXT NOT NULL DEFAULT 'ZAR',
ADD COLUMN     "dataOrigin" "DataOrigin" NOT NULL DEFAULT 'MANUAL',
ADD COLUMN     "stage" "LeadStage" NOT NULL DEFAULT 'LEAD';

-- CreateTable
CREATE TABLE "Session" (
    "id" TEXT NOT NULL,
    "expiresAt" TIMESTAMP(3) NOT NULL,
    "token" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" TIMESTAMP(3) NOT NULL,
    "ipAddress" TEXT,
    "userAgent" TEXT,
    "userId" TEXT NOT NULL,

    CONSTRAINT "Session_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "Account" (
    "id" TEXT NOT NULL,
    "accountId" TEXT NOT NULL,
    "providerId" TEXT NOT NULL,
    "userId" TEXT NOT NULL,
    "accessToken" TEXT,
    "refreshToken" TEXT,
    "idToken" TEXT,
    "accessTokenExpiresAt" TIMESTAMP(3),
    "refreshTokenExpiresAt" TIMESTAMP(3),
    "scope" TEXT,
    "password" TEXT,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "Account_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "Verification" (
    "id" TEXT NOT NULL,
    "identifier" TEXT NOT NULL,
    "value" TEXT NOT NULL,
    "expiresAt" TIMESTAMP(3) NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "Verification_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "RateLimit" (
    "id" TEXT NOT NULL,
    "key" TEXT NOT NULL,
    "count" INTEGER NOT NULL,
    "lastRequest" BIGINT NOT NULL,

    CONSTRAINT "RateLimit_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "BusinessProfile" (
    "id" TEXT NOT NULL,
    "organisationId" TEXT NOT NULL,
    "services" TEXT NOT NULL,
    "customers" TEXT NOT NULL,
    "market" TEXT NOT NULL,
    "conversionGoal" TEXT NOT NULL,
    "completedAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "BusinessProfile_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "ContentRevision" (
    "id" TEXT NOT NULL,
    "contentId" TEXT NOT NULL,
    "number" INTEGER NOT NULL,
    "title" TEXT NOT NULL,
    "body" TEXT NOT NULL,
    "metaTitle" TEXT,
    "metaDesc" TEXT,
    "internalLinks" TEXT NOT NULL DEFAULT '',
    "authorId" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "ContentRevision_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "ContentComment" (
    "id" TEXT NOT NULL,
    "contentId" TEXT NOT NULL,
    "authorId" TEXT NOT NULL,
    "revision" INTEGER NOT NULL,
    "body" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "ContentComment_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "AuditEvent" (
    "id" TEXT NOT NULL,
    "organisationId" TEXT NOT NULL,
    "actorId" TEXT NOT NULL,
    "action" TEXT NOT NULL,
    "entityId" TEXT NOT NULL,
    "detail" JSONB,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "AuditEvent_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "SiteConnection" (
    "id" TEXT NOT NULL,
    "siteId" TEXT NOT NULL,
    "provider" "ConnectionProvider" NOT NULL,
    "status" "ConnectionState" NOT NULL DEFAULT 'NOT_CONNECTED',
    "encryptedCredentials" TEXT,
    "lastSyncedAt" TIMESTAMP(3),
    "error" TEXT,
    "updatedAt" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "SiteConnection_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "AIObservation" (
    "id" TEXT NOT NULL,
    "siteId" TEXT NOT NULL,
    "platform" TEXT NOT NULL,
    "prompt" TEXT NOT NULL,
    "brandMentioned" BOOLEAN NOT NULL,
    "pageCited" BOOLEAN NOT NULL,
    "citationUrl" TEXT,
    "competitorMentioned" TEXT,
    "observedAt" TIMESTAMP(3) NOT NULL,
    "confidence" TEXT NOT NULL,
    "source" TEXT NOT NULL,

    CONSTRAINT "AIObservation_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "Session_token_key" ON "Session"("token");

-- CreateIndex
CREATE INDEX "Session_userId_idx" ON "Session"("userId");

-- CreateIndex
CREATE INDEX "Account_userId_idx" ON "Account"("userId");

-- CreateIndex
CREATE UNIQUE INDEX "Account_providerId_accountId_key" ON "Account"("providerId", "accountId");

-- CreateIndex
CREATE INDEX "Verification_identifier_idx" ON "Verification"("identifier");

-- CreateIndex
CREATE UNIQUE INDEX "RateLimit_key_key" ON "RateLimit"("key");

-- CreateIndex
CREATE UNIQUE INDEX "BusinessProfile_organisationId_key" ON "BusinessProfile"("organisationId");

-- CreateIndex
CREATE UNIQUE INDEX "ContentRevision_contentId_number_key" ON "ContentRevision"("contentId", "number");

-- CreateIndex
CREATE INDEX "AuditEvent_organisationId_createdAt_idx" ON "AuditEvent"("organisationId", "createdAt");

-- CreateIndex
CREATE UNIQUE INDEX "SiteConnection_siteId_provider_key" ON "SiteConnection"("siteId", "provider");

-- CreateIndex
CREATE INDEX "AIObservation_siteId_observedAt_idx" ON "AIObservation"("siteId", "observedAt");

-- CreateIndex
CREATE UNIQUE INDEX "Topic_id_siteId_key" ON "Topic"("id", "siteId");

-- CreateIndex
CREATE INDEX "ContentItem_siteId_scheduledAt_idx" ON "ContentItem"("siteId", "scheduledAt");

-- CreateIndex
CREATE UNIQUE INDEX "ContentItem_id_siteId_key" ON "ContentItem"("id", "siteId");

-- AddForeignKey
ALTER TABLE "ContentItem" ADD CONSTRAINT "ContentItem_topicId_siteId_fkey" FOREIGN KEY ("topicId", "siteId") REFERENCES "Topic"("id", "siteId") ON DELETE NO ACTION ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "Approval" ADD CONSTRAINT "Approval_reviewerId_fkey" FOREIGN KEY ("reviewerId") REFERENCES "User"("id") ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "Publication" ADD CONSTRAINT "Publication_contentId_siteId_fkey" FOREIGN KEY ("contentId", "siteId") REFERENCES "ContentItem"("id", "siteId") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "LeadAttribution" ADD CONSTRAINT "LeadAttribution_contentId_siteId_fkey" FOREIGN KEY ("contentId", "siteId") REFERENCES "ContentItem"("id", "siteId") ON DELETE NO ACTION ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "Session" ADD CONSTRAINT "Session_userId_fkey" FOREIGN KEY ("userId") REFERENCES "User"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "Account" ADD CONSTRAINT "Account_userId_fkey" FOREIGN KEY ("userId") REFERENCES "User"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "BusinessProfile" ADD CONSTRAINT "BusinessProfile_organisationId_fkey" FOREIGN KEY ("organisationId") REFERENCES "Organisation"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "ContentRevision" ADD CONSTRAINT "ContentRevision_contentId_fkey" FOREIGN KEY ("contentId") REFERENCES "ContentItem"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "ContentComment" ADD CONSTRAINT "ContentComment_contentId_fkey" FOREIGN KEY ("contentId") REFERENCES "ContentItem"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "ContentComment" ADD CONSTRAINT "ContentComment_authorId_fkey" FOREIGN KEY ("authorId") REFERENCES "User"("id") ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "AuditEvent" ADD CONSTRAINT "AuditEvent_organisationId_fkey" FOREIGN KEY ("organisationId") REFERENCES "Organisation"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "SiteConnection" ADD CONSTRAINT "SiteConnection_siteId_fkey" FOREIGN KEY ("siteId") REFERENCES "Site"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "AIObservation" ADD CONSTRAINT "AIObservation_siteId_fkey" FOREIGN KEY ("siteId") REFERENCES "Site"("id") ON DELETE CASCADE ON UPDATE CASCADE;

