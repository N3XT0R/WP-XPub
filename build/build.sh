#!/bin/bash

VERSION=$(git describe --tags --always)
echo "<?php const XPUB_VERSION = '$VERSION';" > version.php
echo "Generated version.php with version: $VERSION"
