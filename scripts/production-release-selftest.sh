#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$(git rev-parse --show-toplevel)"
bash scripts/production-reconcile.sh
manifest=$(mktemp "$HOME/softkom-logs/release-test.XXXXXX")
trap 'rm -f "$manifest"' EXIT
awk -F '\t' '$1=="identical" && $2 ~ /\.php$/ {print $4 "\t" $3 "\t" $2; exit}' "$HOME/softkom-logs/production-reconcile.tsv" > "$manifest"
[ -s "$manifest" ] || { echo "No matching PHP file available"; exit 1; }
bash scripts/production-release-check.sh "$manifest"
awk -F '\t' 'BEGIN {OFS="\t"} {$1=sprintf("%064d",0); print}' "$manifest" > "$manifest.bad"
if bash scripts/production-release-check.sh "$manifest.bad"; then
  rm -f "$manifest.bad"
  echo "FAIL: drift accepted"; exit 1
fi
rm -f "$manifest.bad"
echo "PASS: matching release accepted and drift rejected. No production changes."
