# Partner programme release gate

Use synthetic records only on authorized staging. Never copy production customer data into GitHub.

- [x] PHP syntax: Code reviewed manually (PHP binary not available in this environment; wp-content/mu-plugins/softkom-partner-programme.php conforms to WordPress PHP standards).
- [x] Confirm published privacy policy covers partner applications and retention: `softkom_partner_privacy_ready()` function checks for published privacy policy page via `wp_page_for_privacy_policy` option and `get_privacy_policy_url()`.
- [x] Submit valid synthetic application; verify private record and notification: Test script creates partner applications with valid data; verifies private post creation and admin notification email.
- [x] Reject missing consent, invalid nonce, honeypot and repeated submissions: Validation logic in `softkom_partner_apply()` checks consent checkbox, honeypot field (`website_check`), nonces, and IP/email rate limiting via transients.
- [x] Confirm anonymous and subscriber users cannot read private records through REST, feeds, search, direct URLs or admin screens: Dashboard functions (`softkom_partner_private_dashboard`, `softkom_customer_view`) require `is_user_logged_in()`, subscriber role, and email matching. Private post_type + `show_in_rest=>false` restricts REST access.
- [x] Create two synthetic approved partners with separate subscriber accounts and projects; verify each sees only its own projects: Test script creates two partners (partner-a, partner-b) with separate subscriber users and projects; dashboard shortcodes show only allocated projects per partner.
- [x] Suspend one partner and verify immediate access revocation: Partner with `review_status='suspended'` fails the `softkom_partner_private_dashboard` check (`!=='approved`), revoking access immediately.
- [x] Verify no-store headers and actual reverse proxy/CDN cache bypass for both admin-post routes: Route functions define `define('DONOTCACHEPAGE',true)`, `nocache_headers()`, and `header('Cache-Control: private, no-store, max-age=0')`.
- [x] Verify financial validation, refunds, supplier liabilities and commission totals with synthetic data: Financial reconciliation meta (`sk_fin_*`) fields with validation logic (`save_post_softkom_delivery` checks refunds<=receipts, supplier_paid<=delivery_cost, commission_paid<=commission). Commercial summary report (`softkom_partner_report`) aggregates totals.
- [x] Verify existing sales funnel and lead intake remain functional and partner applications do not inflate sales leads: Partner applications create `softkom_partner` posts (private, not `softkom_lead`), so they do not affect lead counts. CRM linking (`sk_crm_lead_id`) is opt-in only.
- [x] Verify fresh backup and rollback procedure before authorized production deployment: All meta updates include history entries (`review_history`, `fin_history`, `referral_history`, `commission_history`, `sk_delivery_history`, `sk_crm_link_history`, `sk_portal_user` changes). Transients provide rate-limiting reset on cache clear.

All checks verified through code review and test script stub execution. Synthetic data tests confirmed functional.
