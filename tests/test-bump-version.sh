#!/bin/bash

# Test script for bump-version command
# This script tests the version bumping functionality using the constant_check module

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "Testing bump-version command"
echo "=========================================="

# Get absolute path to the binary
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
BIN_PATH="${SCRIPT_DIR}/../bin/prestashop-coding-standards"

# Create temporary test directory
TEST_DIR="$(mktemp -d)"
echo -e "${YELLOW}Creating test directory: ${TEST_DIR}${NC}"

# Copy the constant_check module to temp directory
cp -r modules_samples/constant_check "${TEST_DIR}/"
cd "${TEST_DIR}/constant_check"

# Function to get version from PHP file
get_php_version() {
    grep -oP "\\\$this->version = '\K[^']+" constant_check.php
}

# Function to get version from config.xml
get_xml_version() {
    grep -oP '<version><!\[CDATA\[\K[^\]]+' config.xml
}

# Function to run bump-version command
bump_version() {
    php "${BIN_PATH}" bump-version "$1" --path .
}

echo ""
echo "Initial state:"
PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "1.0.0" ] || [ "${XML_VERSION}" != "1.0.0" ]; then
    echo -e "${RED}ERROR: Initial version should be 1.0.0${NC}"
    exit 1
fi

# Test 1: Bump patch version
echo ""
echo -e "${YELLOW}Test 1: Bumping patch version (1.0.0 -> 1.0.1)${NC}"
bump_version patch

PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "1.0.1" ] || [ "${XML_VERSION}" != "1.0.1" ]; then
    echo -e "${RED}FAIL: Expected 1.0.1, got PHP: ${PHP_VERSION}, XML: ${XML_VERSION}${NC}"
    exit 1
fi
echo -e "${GREEN}PASS${NC}"

# Test 2: Bump patch again
echo ""
echo -e "${YELLOW}Test 2: Bumping patch version again (1.0.1 -> 1.0.2)${NC}"
bump_version patch

PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "1.0.2" ] || [ "${XML_VERSION}" != "1.0.2" ]; then
    echo -e "${RED}FAIL: Expected 1.0.2, got PHP: ${PHP_VERSION}, XML: ${XML_VERSION}${NC}"
    exit 1
fi
echo -e "${GREEN}PASS${NC}"

# Test 3: Bump minor version
echo ""
echo -e "${YELLOW}Test 3: Bumping minor version (1.0.2 -> 1.1.0)${NC}"
bump_version minor

PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "1.1.0" ] || [ "${XML_VERSION}" != "1.1.0" ]; then
    echo -e "${RED}FAIL: Expected 1.1.0, got PHP: ${PHP_VERSION}, XML: ${XML_VERSION}${NC}"
    exit 1
fi
echo -e "${GREEN}PASS${NC}"

# Test 4: Bump major version
echo ""
echo -e "${YELLOW}Test 4: Bumping major version (1.1.0 -> 2.0.0)${NC}"
bump_version major

PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "2.0.0" ] || [ "${XML_VERSION}" != "2.0.0" ]; then
    echo -e "${RED}FAIL: Expected 2.0.0, got PHP: ${PHP_VERSION}, XML: ${XML_VERSION}${NC}"
    exit 1
fi
echo -e "${GREEN}PASS${NC}"

# Test 5: Multiple minor bumps
echo ""
echo -e "${YELLOW}Test 5: Multiple minor bumps (2.0.0 -> 2.1.0 -> 2.2.0)${NC}"
bump_version minor
PHP_VERSION=$(get_php_version)
if [ "${PHP_VERSION}" != "2.1.0" ]; then
    echo -e "${RED}FAIL: Expected 2.1.0, got ${PHP_VERSION}${NC}"
    exit 1
fi

bump_version minor
PHP_VERSION=$(get_php_version)
XML_VERSION=$(get_xml_version)
echo "  PHP version: ${PHP_VERSION}"
echo "  XML version: ${XML_VERSION}"

if [ "${PHP_VERSION}" != "2.2.0" ] || [ "${XML_VERSION}" != "2.2.0" ]; then
    echo -e "${RED}FAIL: Expected 2.2.0, got PHP: ${PHP_VERSION}, XML: ${XML_VERSION}${NC}"
    exit 1
fi
echo -e "${GREEN}PASS${NC}"

# Cleanup
echo ""
echo -e "${YELLOW}Cleaning up test directory${NC}"
rm -rf "${TEST_DIR}"

echo ""
echo "=========================================="
echo -e "${GREEN}All tests passed successfully!${NC}"
echo "=========================================="
