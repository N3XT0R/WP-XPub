#!/bin/bash
set -euo pipefail

BUILD_DIR="build"
PLUGIN_DIR="$BUILD_DIR/xpub-multi-channel-publisher"

echo "Cleaning build directory..."
rm -rf "$PLUGIN_DIR"
mkdir -p "$PLUGIN_DIR"

echo "Copying plugin files..."
rsync -av --delete \
  --exclude='.git' \
  --exclude='build' \
  --exclude='tests' \
  --exclude='.github' \
  --exclude='*.zip' \
  --exclude='phpunit.*' \
  --exclude='.gitignore' \
  --exclude='.gitattributes' \
  ./ "$PLUGIN_DIR/"

echo "Contents of build directory:"
ls -la "$PLUGIN_DIR"
