#!/bin/bash

VERSION=$(git describe --tags --always)
echo "<?php define('XPUB_VERSION', '$VERSION');" > version.php
echo "Generated version.php with version: $VERSION"
