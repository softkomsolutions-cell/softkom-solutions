#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$(git rev-parse --show-toplevel)"
root=$(mktemp -d)
trap 'rm -rf "$root"' EXIT
mkdir -p "$root/home/public_html/wp-content/themes/softkom-v3" "$root/home/softkom-backups" "$root/home/softkom-logs" "$root/bin" "$root/repo/scripts" "$root/repo/wp-content/themes/softkom-v3"
cp scripts/production-release-{check,apply}.sh "$root/repo/scripts/"
cd "$root/repo"
git init -q
p=wp-content/themes/softkom-v3/release-test.php
printf '<?php // new version\n' > "$p"
printf '<?php // original version\n' > "$root/home/public_html/$p"
git add .
git -c user.name=Test -c user.email=test@example.invalid commit -qm fixture
a=$(sha256sum "$root/home/public_html/$p" | cut -d ' ' -f1)
b=$(sha256sum "$p" | cut -d ' ' -f1)
printf '%s\t%s\t%s\n' "$a" "$b" "$p" > "$root/manifest"
printf '#!/bin/sh\nexit 22\n' > "$root/bin/curl"
chmod +x "$root/bin/curl"
if HOME="$root/home" SOFTKOM_HEALTH_CURL="$root/bin/curl" bash scripts/production-release-apply.sh "$root/manifest"; then
  echo "FAIL: failed health check accepted"; exit 1
fi
[ "$(sha256sum "$root/home/public_html/$p" | cut -d ' ' -f1)" = "$a" ]
echo "PASS: failed release restored original file"
printf '#!/bin/sh\nexit 0\n' > "$root/bin/curl"
HOME="$root/home" SOFTKOM_HEALTH_CURL="$root/bin/curl" bash scripts/production-release-apply.sh "$root/manifest"
[ "$(sha256sum "$root/home/public_html/$p" | cut -d ' ' -f1)" = "$b" ]
echo "PASS: successful release installed expected file"
echo "Isolated tests complete; real production untouched."
