#!/usr/bin/env sh
# Run from package root: bash scripts/setup-git-hooks.sh
set -e
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  echo "Not a git repository." >&2
  exit 1
fi

git config core.hooksPath .githooks
chmod +x .githooks/commit-msg 2>/dev/null || true
echo "Configured: core.hooksPath=.githooks (commit-msg strips 'Made-with: Cursor' lines)"
