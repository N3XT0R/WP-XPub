#!/bin/bash

for f in languages/xpub-multi-channel-publisher-*.json; do
  locale=$(echo "$f" | sed -E 's/.*xpub-multi-channel-publisher-([a-zA-Z_]+)-[a-f0-9]+\.json/\1/');
  echo "Copying $f to frontend/translations/${locale}.json"
  cp "$f" "frontend/translations/${locale}.json";
done