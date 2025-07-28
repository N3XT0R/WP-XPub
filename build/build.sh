#!/bin/bash
set -e
BUILD_DIR=build
PLUGIN_DIR=$BUILD_DIR/xpub

rm -rf "$PLUGIN_DIR"
mkdir -p "$PLUGIN_DIR"

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