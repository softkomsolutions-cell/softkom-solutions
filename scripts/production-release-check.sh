#!/usr/bin/env bash
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"
live="$HOME/public_html"
manifest="${1:-$HOME/softkom-logs/approved-release.tsv}"
[ -s "$manifest" ] || { echo "No approved release manifest; no changes."; exit 0; }
[ -z "$(git status --porcelain)" ] || { echo "Dirty checkout"; exit 1; }
count=0
while IFS=$'\t' read -r expected target path extra; do
  [[ "$expected" =~ ^[a-f0-9]{64}$ && "$target" =~ ^[a-f0-9]{64}$ && -z "$extra" ]] || exit 1
  [[ "$path" =~ ^wp-content/(themes/softkom-v3/|mu-plugins/)[a-zA-Z0-9_./-]+$ && "$path" != *..* ]] || exit 1
  git ls-files --error-unmatch -- "$path" >/dev/null
  [ -f "$path" ] && [ -f "$live/$path" ] || exit 1
  [ "$(realpath "$path")" = "$PWD/$path" ] && [ "$(realpath "$live/$path")" = "$live/$path" ] || exit 1
  [ "$(sha256sum "$live/$path" | cut -d ' ' -f1)" = "$expected" ] || { echo "Production drift: $path"; exit 1; }
  [ "$(sha256sum "$path" | cut -d ' ' -f1)" = "$target" ] || { echo "Unexpected source: $path"; exit 1; }
  case "$path" in *.php) /usr/local/bin/php -l "$path";; esac
  echo "Preflight passed: $path"
  count=$((count+1))
done < "$manifest"
echo "$count approved files checked. No production changes made."
