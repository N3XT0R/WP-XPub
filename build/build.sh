#!/bin/bash

VERSION=$(git describe --tags --always)
echo "<?php return '$VERSION';" > version.php
echo "Generated version.php with version: $VERSION"
