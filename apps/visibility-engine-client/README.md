# Softkom Visibility Engine Client

External multi-tenant client portal for Softkom's managed visibility service.

## Product boundary

This app is separate from the Softkom WordPress internal Visibility Engine. WordPress remains Softkom's internal operating console. This app is the client-facing SaaS.

## V1 modules

- Organisation and user tenancy
- Connected sites
- Visibility dashboard
- Opportunity queue
- Content calendar
- Content review and approval
- Publishing status
- Search analytics
- AI visibility
- Backlink/referring-domain tracking
- Lead and revenue attribution
- Subscription/billing model

## Data model

Organisation -> Sites -> Topics -> Content -> Approvals -> Publications -> Search Metrics -> Lead Attribution

All client data is scoped by organisation/site.

## Next implementation steps

1. Authentication and organisation switching.
2. API routes backed by Prisma/Postgres.
3. WordPress connector with per-site credentials stored encrypted.
4. Search Console connector.
5. Content approval workflow.
6. Publishing worker and retries.
7. Analytics ingestion.
8. Billing and plan enforcement only after Softkom proves the service internally.
