#!/bin/bash
set -e

PLUGIN_NAME="xpub-multi-channel-publisher"
BUILD_DIR="build"
DIST_DIR="dist"
DEST="$BUILD_DIR/$PLUGIN_NAME"

# Clean up
rm -rf "$DEST" "$DIST_DIR"
mkdir -p "$DEST" "$DIST_DIR"

# Copy plugin files
rsync -a ./ "$DEST/" \
  --exclude='.git' \
  --exclude='build' \
  --exclude='dist' \
  --exclude='tests' \
  --exclude='.github' \
  --exclude='*.zip' \
  --exclude='phpunit.*' \
  --exclude='.gitignore' \
  --exclude='.gitattributes'

# Zip with folder included
cd "$BUILD_DIR"
