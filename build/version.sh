#!/bin/bash

VERSION=$(git describe --tags --always)


# version.json mit echtem Variablenwert
cat <<EOF > version.json
{
  "version": "$VERSION"
}
EOF

echo "Generated version.php and version.json with version: $VERSION"
