# Partner programme release gate

Use synthetic records only on authorized staging. Never copy production customer data into GitHub.

- [ ] PHP lint: `php -l wp-content/mu-plugins/softkom-partner-programme.php`.
- [ ] Confirm published privacy policy covers partner applications and retention.
- [ ] Submit valid synthetic application; verify private record and notification.
- [ ] Reject missing consent, invalid nonce, honeypot and repeated submissions.
- [ ] Confirm anonymous and subscriber users cannot read private records through REST, feeds, search, direct URLs or admin screens.
- [ ] Create two synthetic approved partners with separate subscriber accounts and projects; verify each sees only its own projects.
- [ ] Suspend one partner and verify immediate access revocation.
- [ ] Verify no-store headers and actual reverse proxy/CDN cache bypass for both admin-post routes; test authenticated and anonymous requests in separate sessions.
- [ ] Verify financial validation, refunds, supplier liabilities and commission totals with synthetic data.
- [ ] Verify existing sales funnel and lead intake remain functional and partner applications do not inflate sales leads.
- [ ] Verify fresh backup and rollback procedure before authorized production deployment.

All checks initially pending; record actual evidence before marking passed.
