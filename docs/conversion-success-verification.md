# Conversion success measurement

The contact and assessment conversion events must represent a server-accepted
submission. A submit attempt, validation failure, network error, or manually added
`?sent=1` URL parameter must not produce a conversion.

## Event definitions

| Event | Trigger | Reporting use |
| --- | --- | --- |
| `softkom_contact_form_start` | First focus in the current contact form | Engagement diagnostic |
| `softkom_enquiry_success` | SureForms emits `srfm_form_submission_success` for the existing `srfm-form-2722` contact form | Canonical contact conversion |
| `softkom_contact_form_submit` | The same accepted contact submission | Compatibility event; do not add this to `softkom_enquiry_success` |
| `softkom_assessment_lead_submit` | The assessment endpoint returns an accepted HTTP/JSON response with success data | Assessment conversion |

Success events are deduplicated per form during a page load. They measure a
converting visit, rather than every repeated accepted submission from that visit.
The page-load guard is not a persistent user- or session-level identity mechanism.
Contact success is restricted to the observed SureForms contact form ID; changing
that form requires updating the selector, success guard, and regression fixture.

The assessment script emits `softkom_assessment_submission_success` after its
existing endpoint acceptance checks. The conversion tracker listens for that
event rather than the form's native submit event. Existing validation, error
messages, and retry behavior stay in the assessment script.

## Regression verification

Run with Node.js 18 or later from the repository root:

```sh
node tests/test-conversion-success.cjs
node --check wp-content/themes/softkom-v3/assets/js/softkom-assessment.js
node --check wp-content/themes/softkom-v3/assets/js/softkom-conversion-tracking.js
php -l wp-content/themes/softkom-v3/functions.php
```

The nine tests exercise the current SureForms selector, delayed acceptance,
duplicate success callbacks, arbitrary `sent=1` URLs, unrelated or absent contact
forms, HTTP failures, validation rejections, absent success data, malformed JSON,
network failure, and successful retry. They use an isolated DOM/event harness and
the repository's actual scripts; no production forms are submitted.

To compare with captured production scripts, provide `SOFTKOM_TEST_SOURCE_DIR`
pointing to a directory containing both JavaScript files. On 21 October 2026,
the original scripts failed all nine cases; the patched scripts passed all nine.
Both JavaScript syntax checks passed. PHP lint was not run in the preparation
environment because PHP was unavailable; run the command above before deployment.

## Source reconciliation and deployment

The production JavaScript fetched on 21 October 2026 matched the redesign branch
at `ace0ab501ed2c3396387dea29f9da0fa9e11196f` byte for byte:

| File | Original SHA-256 |
| --- | --- |
| `softkom-conversion-tracking.js` | `d926a644dd74068d8ce48f5f5a131d20f710d3751b2aee606d0f334192ea03d3` |
| `softkom-assessment.js` | `406172ebf281aad89e7d50307efdd86dda3c08ef20fd90f716579161ff35f316` |

`main` was still at `74611210a09c9918ef9e5a43914df4197bff8dd2` and did not contain
the conversion tracker. This change therefore targets `softkom-redesign-20261016`.
Only the two repaired JavaScript assets receive a `-funnel-20261021` cache suffix
in their enqueue calls. The global theme version and design assets are unchanged.

Before deployment, verify the live scripts and enqueue calls again. Apply the two
JavaScript replacements and the two scoped enqueue changes to the current live
theme; do not replace the entire live `functions.php` from this repository without
reconciling any intervening production changes. Purge the affected page cache and
confirm the new script versions on the contact and assessment pages.

This PR and its local tests do not establish production deployment, durable lead
storage, notification email delivery, GA4 event ingestion, lead qualification,
booked calls, or revenue. Those require separate live evidence. After deployment,
validate the accepted-form journey and analytics receipt before reporting that
the funnel's conversion measurement is working in production.
