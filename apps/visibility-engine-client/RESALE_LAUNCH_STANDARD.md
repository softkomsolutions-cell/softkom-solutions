# Visibility Engine Resale Launch Standard

## Product

**Visibility Engine by Softkom** is a managed search, AI-visibility and website-growth platform for SMEs and growing businesses.

It is sold as a SaaS-enabled managed service. Clients receive their own organisation-scoped workspace while Softkom operates the research, opportunity, publishing and optimisation workflow with explicit client approval where required.

The Softkom internal deployment and every external customer use the same core application. Customer-specific data, users, sites, credentials and reporting remain tenant-scoped.

## Commercial packages

| Package | South Africa | International | Sites | Members | Automated AI observations | Best fit |
| --- | ---: | ---: | ---: | ---: | ---: | --- |
| Starter | R1,490/month | US$99/month | 1 | 3 | 3/day | Small business with one primary website |
| Growth | R2,990/month | US$199/month | 3 | 8 | 6/day | Growing business with multiple sites/brands or a larger team |
| Pro | R5,990/month | US$399/month | 10 | 20 | 12/day | Multi-brand, multi-site or agency-like operations |

A 14-day trial includes 1 site, 2 members and 3 AI observations per day. No payment details are required for the trial. South African customers are billed in ZAR and international customers can be billed in USD; the subscription record stores the billing currency explicitly.

### Paid activation

The launch baseline supports two commercial paths:

1. **Managed invoicing** — Softkom invoices the customer and activates the selected plan from the operator/admin console after payment or agreed commercial approval.
2. **Connected checkout** — optional BILLING_STARTER_URL, BILLING_GROWTH_URL and BILLING_PRO_URL links can route customers to a configured payment provider. The authenticated billing webhook updates subscription state.

The product must never imply that a payment has succeeded until the subscription record is updated. Paid subscription records must persist both the amount and its currency (`ZAR` or `USD`).

## Core inclusions

All paid packages include:

- organisation-isolated client workspace
- public trial signup and secure sign-in
- site onboarding and business profile
- Google Search Console connection and synchronisation
- Google Analytics 4 connection and lead-event reporting
- AI visibility observations with evidence/provider attribution
- evidence-backed opportunity queue
- content proposals, revisions and approval workflow
- WordPress connection and controlled publishing
- publication status, retries and verification
- lead attribution and commercial funnel reporting
- monthly visibility/business reporting
- member invitations subject to plan limits
- audit trail and encrypted external credentials

## Managed-service operating model

Softkom remains responsible for:

- opportunity review and prioritisation
- quality control of proposed website/content changes
- monitoring provider connection health
- publication recovery and retry review
- client reporting interpretation
- plan activation when managed invoicing is used
- support and escalation

Clients are expected to:

- connect or authorise their website/data sources
- confirm their business profile and conversion goals
- approve or request changes to material content
- provide access needed for WordPress publishing
- keep billing and account contact details current

## Client onboarding acceptance

A new client is considered onboarded only when all applicable gates pass:

1. Account created and email/password sign-in works.
2. Organisation is created and the client is OWNER of only that organisation.
3. Website is added under the correct organisation.
4. Business services, audience, market and conversion goal are stored.
5. Search Console shows REQUIRES_SETUP until connected, then the selected property is persisted.
6. GA4 shows REQUIRES_SETUP until connected, then the selected property is persisted.
7. WordPress shows REQUIRES_SETUP until valid credentials are supplied.
8. No other organisation/site/user data is visible.
9. Plan limits are enforced for sites and members.
10. Trial expiry blocks write actions without deleting client data.
11. An invitation can be accepted only by the invited email.
12. At least one real or controlled provider sync completes without fake performance data.
13. If WordPress publishing is enabled, an approved test item publishes and its public URL verifies.
14. Lead attribution intake records a marked test lead without exposing contact details to analytics.
15. Logout revokes the session and protected routes require authentication.

## Softkom internal acceptance

Softkom's own workspace is release-ready only when:

- /api/health returns success
- /api/ready confirms database reachability
- web and worker deployments are green
- the latest worker execution succeeds
- Search Console sync works on the selected Softkom property
- GA4 sync works on the selected Softkom property
- AI monitoring creates truthful provider-labelled observations
- the WordPress bridge is healthy
- website assessment/form leads reach attribution
- client notifications reach the intended mailbox
- no pending destructive Railway changes exist
- no client-facing page displays demo/fake metrics as live data

## Security and tenant-isolation baseline

Required before every commercial release:

- organisation membership verified server-side for every scoped request
- site/record IDs cannot expand access beyond the active organisation
- external credentials encrypted at rest
- secrets never returned to client components or logs
- public signup rate-limited
- secure production cookies and origin checks active
- WordPress/Google HTTP connections reject private/internal destinations
- invitation tokens stored hashed and expire
- audit events created for material admin/workflow actions
- no production fixture/test data is mixed into client reporting

## Branding and resale position

The default commercial brand is **Visibility Engine by Softkom**.

This is not presented as a generic white-label platform at launch. The architecture remains tenant-safe and can support branded editions later, but launch acceptance does not depend on customer-controlled logos/domains/themes.

Public copy should avoid internal codenames such as AutoSEO. Internal compatibility identifiers may remain in code where changing them would risk live integrations.

## Current non-baseline items

The following are not required to sell the managed V1 unless separately contracted:

- Shopify/Webflow automated publishing
- CRM-native two-way sync
- backlink purchasing or exchange automation
- customer-controlled white-label branding
- arbitrary SEO-plugin custom metadata integrations
- fully automatic publication without approval
- unsupported claims of AI ranking or guaranteed revenue

They can be packaged as future add-ons only after independently validated.

## Release gate

A resale release is green only when all of the following are true:

- branch typecheck passes
- unit/integration tests pass
- lint passes
- production build passes
- tenant-isolation tests pass
- browser onboarding workflow passes
- current staging web deployment is healthy
- current staging worker execution succeeds
- live /api/health and /api/ready pass
- signup, onboarding, pricing, login, logout and trial enforcement are verified
- current product documentation matches what the application actually does

Do not market a feature as live merely because a schema, environment variable or UI placeholder exists.
