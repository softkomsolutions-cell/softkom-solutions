# Softkom Solutions

Custom WordPress application code for the Softkom Solutions lead generation,
assessment, attribution, campaign intelligence, pipeline and recurring revenue system.

## Primary application

wp-content/themes/softkom-v3/

## Custom MU plugin

wp-content/mu-plugins/softkom-sureforms-guard.php

## Important

This repository intentionally excludes:
- WordPress core
- wp-config.php
- uploads
- backups
- database dumps
- third-party plugins
- Local runtime files
- debug logs

Development should preserve the existing acquisition flow:

Tracked Campaign
-> Assessment
-> Lead Scoring
-> Security / Routing
-> Attribution
-> HOT Lead Auto-Pipeline
-> Recurring Recommendation
-> Estimated MRR
-> Campaign / Acquisition Reporting
