#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$(git rev-parse --show-toplevel)"
live="$HOME/public_html"
logs="$HOME/softkom-logs"
mkdir -p "$logs"
exec 9>"$logs/production-release.lock"
flock -n 9
manifest="${1:-$logs/approved-release.tsv}"
[ -s "$manifest" ] || { echo "No approved release; no changes."; exit 0; }
[ "$(awk 'END {print NR}' "$manifest")" = 1 ] || { echo "Only single-file releases supported"; exit 1; }
bash scripts/production-release-check.sh "$manifest"
IFS=$'\t' read -r expected target path < "$manifest"
[ "$expected" != "$target" ] || { echo "Already identical; no changes."; exit 0; }
backup=$(mktemp -d "$HOME/softkom-backups/release.XXXXXX")
cp -p "$live/$path" "$backup/original"
[ "$(sha256sum "$backup/original" | cut -d ' ' -f1)" = "$expected" ]
tmp=$(mktemp "$(dirname "$live/$path")/.softkom-release.XXXXXX")
changed=0
cleanup() {
  rc=$?
  trap - EXIT
  if [ "$changed" = 1 ] && [ "$rc" != 0 ]; then
    cp -p "$backup/original" "$tmp" && mv -f "$tmp" "$live/$path" || { echo "ROLLBACK FAILED: $backup"; exit 2; }
    echo "Original file restored: $path"
  fi
  rm -f "$tmp"
  exit "$rc"
}
trap cleanup EXIT
trap 'exit 1' HUP INT TERM
cp -p "$path" "$tmp"
chmod --reference="$live/$path" "$tmp"
[ "$(sha256sum "$tmp" | cut -d ' ' -f1)" = "$target" ]
[ "$(sha256sum "$live/$path" | cut -d ' ' -f1)" = "$expected" ]
changed=1
mv -f "$tmp" "$live/$path"
[ "$(sha256sum "$live/$path" | cut -d ' ' -f1)" = "$target" ]
health_curl="${SOFTKOM_HEALTH_CURL:-/usr/bin/curl}"
for url in https://softkomsolutions.com/ https://softkomsolutions.com/assessment/; do
  if ! "$health_curl" -fsSL --max-time 45 -o /dev/null "$url"; then
    echo "Health check failed: $url" >&2
    exit 1
  fi
done
changed=0
echo "Released: $path; recovery copy: $backup"
