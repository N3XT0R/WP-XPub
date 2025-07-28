<?php

include_once 'version.php';

/*
Plugin Name: xPub
Description: Modular Auto-Publisher for WordPress to external platforms (dev.to, LinkedIn, etc.)
Author: php-dev
Version: 1.0.0-RC1
Domain Path: /languages
*/

use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
