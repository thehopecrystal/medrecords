#!/usr/bin/env bash
#
# Guard against a bloated vendor/ directory.
#
# Why this exists: when Composer's GitHub credentials are invalid or its API
# rate limit is exhausted, `composer install` silently falls back to installing
# packages from *source* (git clone) instead of from the dist zipball. Source
# installs carry each package's full .git history, which inflates vendor/ by
# hundreds of megabytes (phpunit/phpunit alone: ~433MB -> ~6MB from dist).
#
# This script reports:
#   * any package installed from source (should always be zero)
#   * any leftover nested .git directory inside vendor/
#   * the resulting vendor/ size
#
# Usage:  bash tools/check-vendor-size.sh
# Exit code: 0 = healthy, 1 = bloated (fix with the command printed below)

set -uo pipefail

cd "$(dirname "$0")/.." || exit 1

VENDOR_DIR="vendor"

if [ ! -d "$VENDOR_DIR" ]; then
    echo "vendor/ not found — nothing to check."
    exit 0
fi

echo "=== vendor/ size report ==="

# 1. Nested .git directories left behind by source installs.
NESTED_GIT=$(find "$VENDOR_DIR" -maxdepth 3 -type d -name ".git" 2>/dev/null | wc -l | tr -d ' ')

# 2. Packages Composer recorded as installed from source.
SOURCE_COUNT=0
if [ -f "$VENDOR_DIR/composer/installed.json" ]; then
    SOURCE_COUNT=$(php -r '
        $j = json_decode(file_get_contents($argv[1]), true);
        $n = 0;
        foreach (($j["packages"] ?? $j) as $p) {
            if (($p["installation-source"] ?? "") === "source") {
                $n++;
                echo "  source: {$p["name"]}\n";
            }
        }
        echo $n;
    ' "$VENDOR_DIR/composer/installed.json" | tail -1)
fi

VENDOR_MB=$(du -sm "$VENDOR_DIR" 2>/dev/null | cut -f1)

echo "vendor/ size        : ${VENDOR_MB} MB"
echo "nested .git dirs    : ${NESTED_GIT}"
echo "source-installed pkgs: ${SOURCE_COUNT}"

if [ "$NESTED_GIT" -gt 0 ] || [ "$SOURCE_COUNT" -gt 0 ]; then
    echo ""
    echo "FAIL: vendor/ is bloated."
    echo ""
    echo "Fix by reinstalling every package from the dist zipball:"
    echo ""
    echo "  composer config --global --unset github-oauth.github.com"
    echo "  composer reinstall --prefer-dist --no-interaction"
    echo ""
    echo "Then re-run this script to confirm."
    exit 1
fi

echo ""
echo "OK: vendor/ is clean (all packages installed from dist)."
exit 0
