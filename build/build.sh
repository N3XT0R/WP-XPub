#!/bin/bash
set -e

DIST_DIR=build/dist
PLUGIN_NAME=xpub-multi-channel-publisher
PLUGIN_DIR=$DIST_DIR/$PLUGIN_NAME

rm -rf "$DIST_DIR"
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
