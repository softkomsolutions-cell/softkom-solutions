The client workspace now publishes an approved revision to WordPress, verifies
its live URL, connects Google Search Console and displays persisted search
measurements against that content. Existing tenant boundaries, approval history
and lead/revenue rules are preserved. Keep this PR in draft; no production merge
or deployment is included.

- Standard WordPress Application Passwords, encrypted credentials, connection
  health, permissions checks and reconnect/disconnect actions.
- Public HTTPS destination checks, DNS/IP pinning, safe redirects, bounded
  requests and secret-free errors/audits.
- Atomic fenced publication jobs, exact requested snapshots, deterministic remote
  reconciliation, retained WordPress IDs, verification, backoff and attempt limits.
- Google OAuth with state/PKCE and encrypted refresh tokens, accessible property
  selection, manual/daily sync jobs and property-isolated measurement upserts.
- Data freshness/coverage, page/query trends, editable branded terms and mapped
  content/cluster/lead reporting using persisted observations.
- Optional reviewer assignments now restrict approval to the named authorised
  organisation reviewer; managers retain only the explicit audited exception.
- Additive migrations 004 and 005 preserve existing V1 data and have individual
  transaction rollback and successful reapplication evidence.

Validation: 36 unit/database/real-WordPress tests and 10 browser tests, none
skipped; schema/migration checks, typecheck, lint, production build and dependency
audit pass. Real disposable WordPress evidence includes valid/wrong/insufficient
Application Passwords, publication, public verification and same-ID retry.
Google evidence is explicitly controlled OAuth/API fixture validation, not a
real Google-account test. Screenshots and detailed design/validation are in
`docs/visibility-engine-client-v1/IMPLEMENTATION.md`.

Real Google consent/refresh/revocation and production egress/supervised scheduling
still need staging validation with a configured Google project/account. Generic
SEO-plugin meta fields and arbitrary WordPress plugin compatibility are not
claimed as configured. GA4, AI monitoring, CRM imports, comment anchors, billing,
backlinks, additional CMSs and cross-client administration remain deferred.

This text is prepared for PR #38 because GitHub REST/GraphQL API access is denied
in the current environment. Branch commits and push are handled through Git.
