#!/usr/bin/env bash
set -euo pipefail
umask 077
repo=$(git rev-parse --show-toplevel)
logs="$HOME/softkom-logs"
mkdir -p "$logs" "$HOME/softkom-backups"
old=$(mktemp "$logs/cron.XXXXXX")
new=$(mktemp "$logs/cron.XXXXXX")
trap 'rm -f "$old" "$new"' EXIT
crontab -l > "$old"
cp "$old" "$HOME/softkom-backups/crontab-before-release-$(date +%Y%m%d-%H%M%S).txt"
awk '!/# softkom-approved-release$/' "$old" > "$new"
printf '7,22,37,52 * * * * /bin/flock -n %s/git-sync.lock /bin/bash -c %s >> %s/production-release.log 2>&1 # softkom-approved-release\n' "$logs" "'cd \"$repo\" && unset SOFTKOM_HEALTH_CURL && if [ -s \"$logs/approved-release.tsv\" ]; then /bin/bash scripts/production-release-apply.sh && mv \"$logs/approved-release.tsv\" \"$logs/last-applied-release.tsv\"; else echo \"No approved release; no changes.\"; fi'" "$logs" >> "$new"
crontab "$new"
crontab -l
echo "Scheduled approved releases installed; no production files changed."
