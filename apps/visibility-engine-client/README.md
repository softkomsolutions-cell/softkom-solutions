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
secret. Public signup is enabled for a 14-day trial. Production sample performance is never seeded or fabricated.

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
6. An owner, administrator or Softkom manager connects a public HTTPS WordPress
   installation with a standard Application Password. The account must be able
   to create, publish and edit pages. Credentials are encrypted and never redisplayed.
7. Request publication of the approved revision. A worker publishes that stored
   snapshot, retains the remote WordPress ID, verifies the public page and then
   marks it LIVE. Failures retain safe recovery details and use bounded retries.
8. Connect Google Search Console, select an accessible URL-prefix or domain
   property, then request a sync. Persisted observations feed search, published
   content and cluster views; ordinary dashboard reads make no provider calls.

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
- `lib/publishing`: WordPress REST provider and atomic, leased publication
  worker. The requested revision supplies content, metadata and funnel routing.
  Deterministic remote slugs reconcile lost responses; remote IDs survive failed
  public verification. Other CMS providers remain unavailable.
- `lib/analytics`: Google OAuth/PKCE, encrypted token refresh, property access
  verification and leased Search Console jobs. GSC results are keyed by site,
  property, date, query, page and source. Other search/AI connectors remain unavailable.
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
`lib/connections/http.ts` requires HTTPS, rejects embedded credentials and
non-public destinations, validates every DNS result and redirect, pins the
validated IP to the TLS socket, rejects credential forwarding across origins,
and bounds DNS, response size and total request/operation time. No production
flag bypasses private-address or certificate validation.

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
npm run test:services # Docker: disposable real WordPress + MariaDB
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

## Workers and recovery

Run `npm run worker` once per minute from your process supervisor or scheduler.
Each invocation schedules eligible daily GSC syncs, processes up to 20 publication
jobs and 10 search jobs, and exits. Multiple invocations cooperate through atomic
compare-and-set claims and fencing tokens. No HTTP call runs inside a database
transaction. Job leases last ten minutes; network operations have shorter hard
limits. Attempts stop at five, with exponential backoff capped at one hour.
An expired final attempt becomes FAILED instead of remaining PUBLISHING.

Job state, last attempt, next retry, verification, last successful sync and safe
failure reasons are persisted and shown in the portal. No scheduler has been
deployed by this change. A process supervisor must invoke the command; a planned
content date does not automatically authorise publication. Managers may retry
failed publications below the attempt limit. At the limit, inspect the remote
resource and failure before arranging a new approved request; do not reset or
silently discard its remote ID.

## Google configuration and data semantics

Set `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_CALLBACK_URL`, and the
existing `CONNECTION_ENCRYPTION_KEY` securely. Enable the Search Console API,
configure Google's consent screen and register the exact callback
`https://your-workspace.example/api/connections/google/callback`. Production
requires a matching HTTPS auth/callback origin. The requested scope is
`https://www.googleapis.com/auth/webmasters.readonly`, with offline consent and
PKCE. OAuth state is single-use, ten-minute, user/organisation/site/generation
bound. Access and refresh tokens stay inside encrypted server-side envelopes.

Properties are fetched during authorisation and rechecked during selection and
sync. URL-prefix and `sc-domain:` properties are supported. Syncs request finalised
web-search rows over a rolling 28-day window ending three UTC calendar days ago.
They do not request today's partial results. Coverage, latest returned data date,
last success/failure and retry time remain explicit. Google may omit anonymised
or low-volume queries: observed query/page totals are not a full traffic census.
A property change retains old observations separately and hides them from the
new property's dashboard. Manual and other-provider observations are preserved.

Branded terms are edited in Connections, one phrase per line. Matching is NFKC,
case-insensitive and whole-phrase. Empty terms leave queries unclassified. Page
and cluster mappings use recorded, verified live URLs; tracking parameters and
fragments are ignored, meaningful query parameters remain distinct. Leads remain
recorded attribution, not proof of causation; won revenue/currencies keep their
existing rules. Trend views explain date coverage and never invent volume.

## Review permissions

Reviewer assignment is optional. Assigned reviewers must be OWNER, ADMIN or
CLIENT members of the same organisation; only that reviewer may approve or
request changes. All current organisation members can comment. Without an
assignment, OWNER, ADMIN and CLIENT may approve/request changes. A SOFTKOM_MANAGER
cannot approve as a client; the existing explicit, audited override is retained.
A new revision always requires a new approval. Workers use the approved requested
snapshot even if a newer revision is waiting for review.

## Integration evidence and limitations

`npm run test:services` creates **only disposable named Docker services** and
stores generated local WordPress Application Passwords in ignored
`.test-services.json`. `npm run test:services -- --stop` removes those services.
Unit/database tests include a real WordPress REST test for successful auth,
wrong credentials, insufficient rights, publication, public verification and
same-ID retry. There is no production WordPress test.

`npm run test:browser` creates an ignored local TLS certificate, trusts that
exact fixture certificate, and runs a production Next.js build behind a fixture
HTTPS proxy. A test-only Node preloader routes only the explicit WordPress and
Google fixture hosts to loopback. Production application code never loads it.
The browser completes WordPress connection, approval, publication and live URL,
then controlled Google consent/state/PKCE, property selection, ingestion and
persisted dashboard metrics. This is **controlled Google evidence, not real
Google-account validation**. No Google account credentials or production keys
are needed in CI. See IMPLEMENTATION.md for executed results and screenshots.

WordPress core page title and excerpt are written. An arbitrary SEO plugin's
separate meta-title/description fields are not registered or claimed as configured.
Remote deterministic slugs retain the approved slug prefix plus an identity
suffix. Standard WordPress does not offer a transactionally atomic external
idempotency-key API; leased workers plus deterministic slug reconciliation and
persisted IDs prevent duplicates in the tested retry/concurrency paths. A
third-party WordPress plugin that changes slugs, strips identity markers or
hides pages from REST requires explicit compatibility validation.

Real Google consent/refresh/revocation, provider quotas and deployment egress remain staging checks requiring configured provider accounts. Search Console, GA4, automated AI observations, WordPress publishing, secure attribution intake, trial signup, invitations, plan limits and subscription-state enforcement are implemented. External payment checkout remains optional: paid plans may be activated by Softkom after invoicing, or via BILLING_* checkout URLs plus the authenticated billing webhook. The commercial model supports ZAR billing for South African customers and USD billing for international customers, with amount and currency stored together on the subscription. CRM imports, general inline comment anchors, backlink acquisition, additional CMS publishing providers and broad cross-client administration remain outside the current resale baseline.
