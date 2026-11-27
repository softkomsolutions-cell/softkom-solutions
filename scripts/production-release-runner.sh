#!/usr/bin/env bash
set -euo pipefail
umask 077
cd "$HOME/home/wwcgbqzj/softkom-src"
unset SOFTKOM_HEALTH_CURL
logs="$HOME/softkom-logs"
date -u
manifest="$logs/approved-release.tsv"
if [ ! -s "$manifest" ]; then
  echo "No approved release; no changes."
  exit 0
fi
bash scripts/production-release-apply.sh "$manifest"
mv "$manifest" "$logs/last-applied-release.tsv"
