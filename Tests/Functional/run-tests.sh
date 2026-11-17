#!/usr/bin/env bash
#
# Runs the functional tests of this package in a throwaway Neos distribution.
#
# Usage: [NEOS_VERSION=9.2] Tests/Functional/run-tests.sh [target-directory] [-- additional phpunit arguments]
#
# The distribution is created on the first run (see setup-test-distribution.sh) and reused afterwards.
# Delete the target directory to start from scratch.
#
# Examples:
#   Tests/Functional/run-tests.sh
#   Tests/Functional/run-tests.sh /tmp/listable-tests -- --filter PaginationArrayTest
#   NEOS_VERSION=9.0 Tests/Functional/run-tests.sh
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export NEOS_VERSION="${NEOS_VERSION:-9.2}"
TARGET_DIR="${TMPDIR:-/tmp}/flowpack-listable-test-distribution-neos-$NEOS_VERSION"
if [[ $# -gt 0 && "$1" != "--" ]]; then
    TARGET_DIR="$1"
    shift
fi
if [[ $# -gt 0 && "$1" == "--" ]]; then
    shift
fi
TARGET_DIR="${TARGET_DIR%/}"

if [[ ! -x "$TARGET_DIR/bin/phpunit" ]]; then
    "$SCRIPT_DIR/setup-test-distribution.sh" "$TARGET_DIR"
fi

cd "$TARGET_DIR"
# memory_limit = -1 for PHPUnit and every PHP process it starts (see setup-test-distribution.sh).
# The leading ":" keeps the system's scan dir and adds ours.
export PHP_INI_SCAN_DIR=":$TARGET_DIR/php-ini"
exec bin/phpunit \
    -c Build/BuildEssentials/PhpUnit/FunctionalTests.xml \
    "$@" \
    Packages/Application/Flowpack.Listable/Tests/Functional
