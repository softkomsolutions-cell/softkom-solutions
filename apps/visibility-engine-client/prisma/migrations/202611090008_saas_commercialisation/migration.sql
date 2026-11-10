ALTER TABLE "Subscription"
  ALTER COLUMN "plan" SET DEFAULT 'TRIAL',
  ALTER COLUMN "status" SET DEFAULT 'TRIALING',
  ALTER COLUMN "monthlyPrice" SET DEFAULT 0;

ALTER TABLE "Subscription"
  ADD COLUMN IF NOT EXISTS "trialStartedAt" TIMESTAMP(3),
  ADD COLUMN IF NOT EXISTS "trialEndsAt" TIMESTAMP(3),
  ADD COLUMN IF NOT EXISTS "currentPeriodEndsAt" TIMESTAMP(3),
  ADD COLUMN IF NOT EXISTS "provider" TEXT,
  ADD COLUMN IF NOT EXISTS "providerCustomerId" TEXT,
  ADD COLUMN IF NOT EXISTS "providerSubscriptionId" TEXT,
  ADD COLUMN IF NOT EXISTS "cancelAtPeriodEnd" BOOLEAN NOT NULL DEFAULT false;

CREATE TABLE IF NOT EXISTS "Invitation" (
  "id" TEXT NOT NULL,
  "organisationId" TEXT NOT NULL,
  "email" TEXT NOT NULL,
  "role" "OrgRole" NOT NULL DEFAULT 'CLIENT',
  "tokenHash" TEXT NOT NULL,
  "invitedBy" TEXT NOT NULL,
  "expiresAt" TIMESTAMP(3) NOT NULL,
  "acceptedAt" TIMESTAMP(3),
  "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT "Invitation_pkey" PRIMARY KEY ("id"),
  CONSTRAINT "Invitation_organisationId_fkey"
    FOREIGN KEY ("organisationId") REFERENCES "Organisation"("id")
    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS "Invitation_tokenHash_key"
  ON "Invitation"("tokenHash");
CREATE INDEX IF NOT EXISTS "Invitation_organisationId_email_idx"
  ON "Invitation"("organisationId", "email");
