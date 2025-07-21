<?php
/*
Plugin Name: xPub
Description: Modular Auto-Publisher for WordPress to external platforms (dev.to, LinkedIn, etc.)
Author: php-dev
*/

defined('ABSPATH') or die('No script kiddies please!');

require_once __DIR__.'/vendor/autoload.php';

\N3XT0R\XPub\Plugin::init(__FILE__);
