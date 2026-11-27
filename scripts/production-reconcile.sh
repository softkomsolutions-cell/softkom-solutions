#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$(git rev-parse --show-toplevel)"
live="$HOME/public_html"
out="$HOME/softkom-logs"
mkdir -p "$out"
report="$out/production-reconcile.tsv"
tmp=$(mktemp "$out/reconcile.XXXXXX")
trap 'rm -f "$tmp"' EXIT
printf 'status\tpath\trepository_sha256\tproduction_sha256\n' > "$tmp"
git ls-files -z wp-content/themes/softkom-v3 wp-content/mu-plugins | while IFS= read -r -d '' f; do
  [ -f "$f" ] && [ ! -L "$f" ] || continue
  a=$(sha256sum "$f" | cut -d ' ' -f1)
  b=-
  status=missing
  if [ -L "$live/$f" ]; then
    status=symlink
  elif [ -f "$live/$f" ]; then
    b=$(sha256sum "$live/$f" | cut -d ' ' -f1)
    status=different
    [ "$a" != "$b" ] || status=identical
  fi
  printf '%s\t%s\t%s\t%s\n' "$status" "$f" "$a" "$b"
done >> "$tmp"
mv "$tmp" "$report"
echo "Repository commit: $(git rev-parse HEAD)"
awk -F '\t' 'NR>1 {n[$1]++} END {for (s in n) print s, n[s]}' "$report"
echo "Private inventory: $report"
echo "No production files changed."
