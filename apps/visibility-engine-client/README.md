# Softkom Visibility Engine — client workspace

An external, organisation-scoped Next.js SaaS for managed commercial visibility.
WordPress remains Softkom's internal operating console. This application neither
loads WordPress nor modifies the live Softkom site.

## Run locally

Use Node 22.12+ (validated with Node 24) and PostgreSQL 17. From this directory:

```sh
npm ci
cp .env.example .env
# Set DATABASE_URL to a local PostgreSQL database and generate a strong
# BETTER_AUTH_SECRET. Never commit actual configuration or print secrets.
npm run db:generate
npm run db:validate
npm run db:migrate
npm run dev
```

`BETTER_AUTH_URL` must match the application's exact origin. Use HTTPS outside
local development. Production startup fails without a strong authentication
secret. There is no public signup, demo seeding, or production sample performance.

Provision accounts through the server-side operator command. Supply
`PROVISION_EMAIL`, `PROVISION_NAME`, `PROVISION_PASSWORD` (12–128 characters),
and optionally `PROVISION_ORGANISATION_ID` and `PROVISION_ROLE` through a secure
process environment, then run `npm run provision`. Passwords are scrypt-hashed
using Better Auth's password implementation and are never printed. Re-provisioning
resets the password and revokes all existing sessions for that user. With no
membership, the user completes onboarding and becomes their workspace's owner.
Normal invited client users should receive a specific organisation and CLIENT
role. Roles are OWNER, ADMIN, CLIENT, and SOFTKOM_MANAGER. No role confers access
to organisations where the user has no membership.

## Workflow

1. Onboard the organisation, website, offers, customers, market and conversion goal.
2. A manager records an opportunity with research reasoning, evidence, intent,
   business problem, priority, cluster and funnel destination.
3. A manager proposes content, metadata, links, a reviewer and an optional planned date.
4. Clients approve, request changes, or comment on an identified revision.
5. A new revision returns to review; earlier approvals cannot authorise it.
6. A manager requests publication. Until a connector exists, the request is
   recorded as **Failed**, with a reason and no live URL. No content is sent.
7. Stored search observations, AI evidence, attribution and monthly reporting
   connect activity to outcomes without inventing measurements.

Calendar dates are planning records, not an active publishing scheduler. A
SOFTKOM_MANAGER can explicitly override approval with a documented reason of at
least 20 characters; the override is audited. Managers cannot silently act as
the client approver. A live URL is offered only for a LIVE publication record.

## Architecture and security

- `lib/auth.ts`: Better Auth, database-backed sessions, disabled public signup,
  database-backed rate limiting and secure production cookies. Sign-out revokes
  the server session; credentials are never stored in browser storage.
- `lib/context.ts`: authenticated user, current membership and scoped record
  lookups. The organisation cookie is only a selector: every request verifies it
  against the session user's memberships. An invalid selection opens the
  forbidden state. Site filters and record IDs cannot expand access.
- `lib/workflow.ts`: validated business mutations, role checks, serializable
  revision/approval transactions, scoped updates and transactional audit writes.
- `lib/queries.ts`: organisation/site-scoped reads without a shared tenant cache.
  Search CTR is total clicks divided by total impressions; average position is
  impression-weighted. Trend comparisons explain their observation coverage.
- `lib/publishing`: provider contract and truthful unavailable implementation;
  future workers must atomically claim PENDING records, publish the requested
  revision snapshot and use the publication ID for idempotency. Never publish
  arbitrary current copy after a revision-specific approval.
- `lib/analytics`: separate search and AI provider interfaces. No provider is
  registered or represented as connected without an implemented integration.
- `lib/connections`: server-side AES-256-GCM credential envelopes with random
  nonces. A base64-encoded 32-byte `CONNECTION_ENCRYPTION_KEY` is required before
  credential storage is enabled. Credential values are never selected for UI
  props. Key rotation must re-encrypt existing envelopes through an operator job.
- `components`: server-rendered product views plus small interactive forms.
  Next server actions enforce origin checks; Better Auth validates auth origins.
- `middleware.ts`: request-specific CSP nonces, no-store responses, frame
  protection, restrictive browser permissions and nosniff headers. Dynamic root
  rendering ensures hydration scripts carry the current nonce.

HTML is allowlist-sanitised both when written and when displayed. Same-site
composite foreign keys prevent content/topic/publication/attribution links from
pointing at records on another site. A session and an organisation membership
are required independently; there is no manager bypass of tenant scope.

Deploy behind a trusted HTTPS proxy that strips client-supplied forwarding/IP
headers and sets its own. This is required for reliable IP-based rate limiting.
Set the exact public auth origin, protect operator access, use a least-privilege
application database account, and run migrations with a separate DDL account.
Provider implementations must add destination/SSRF validation before making
server-side requests to client-supplied website URLs.

## Database migrations

The existing models and fields are retained. Migrations establish the original
schema, add authentication/business/review/connection/AI/audit structures and
same-site constraints, then snapshot legacy content and give old sites truthful
connection states. No content, users or subscription models are removed.

Prisma's JavaScript query engine and PostgreSQL adapter avoid native query-engine
downloads. SQL is generated with Prisma's WASM schema engine. Prisma 6's
experimental WASM **migration deployment** adapter cannot decode PostgreSQL's
catalog `name` type. Therefore `npm run db:migrate` uses `scripts/migrate.ts`:
it applies the same generated SQL with `pg`, a PostgreSQL advisory lock,
transactions and SHA-256 verification, and maintains the standard
`_prisma_migrations` ledger. It refuses changed checksums, unknown migrations,
unfinished migrations and an unbaselined existing schema. The SQL and ledger
remain compatible with native-engine Prisma tooling.

For an existing database without a migration ledger, first back it up and verify
it matches the original scaffold schema, then use native Prisma tooling to
resolve `202611030001_baseline` as applied. Do not execute the baseline CREATE
statements over existing tables. Inspect existing cross-site inconsistencies
before adding the composite foreign keys; a mismatch must be corrected explicitly.
Never use `db push --accept-data-loss`, a reset, or an automatic destructive fallback.

Migration tests create their own temporary database. They prove existing content
survives, legacy snapshots are retained, repeated application is harmless,
changed checksums are rejected and partial failing migrations roll back. The
test database user needs CREATEDB; production application users do not.

## Verification

Use an isolated test database, not a production database:

```sh
npm run db:generate
npm run db:validate
npm run db:migrate
npm run typecheck
npm run lint
npm test
npm run build
npx playwright install chromium
npm run test:browser
npm audit --omit=dev
```

When Chromium is already installed, set
`PLAYWRIGHT_CHROMIUM_EXECUTABLE=/usr/bin/chromium` instead of downloading it.
Browser tests launch the production server and exercise real auth, onboarding,
research/content creation, revision requests, approval history, unavailable
publishing, foreign IDs/site filters/organisation cookies, reports, mobile
layout and logout. They respect the actual login rate limiter on repeated runs.
All fixture users and organisations are removed afterwards. Browser traces and
storage states can contain test session tokens; keep generated artifacts private.

## Deliberately unavailable integrations

WordPress publishing, Search Console, GA4, CRM ingestion and automated AI
observations have no registered live connectors. The UI says so. There is no
background publisher, automatic scheduling, OAuth credential-entry screen,
fabricated AI score, search-volume estimate, CRM success or demo revenue.
Persisted observations/results display when available; manual attribution is
explicitly labelled. Imported data retains its source. Revenue is recognised
only on won records and is never mixed with estimated MRR or summed across
currencies. Billing, public signup and cross-client administration are out of scope.
