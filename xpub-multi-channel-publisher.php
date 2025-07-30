<?php
/**
 * Plugin Name: XPub Multi-Channel Publisher
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 1.2.0-dev
 * Author: Ilya Beliaev
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: xpub-multi-channel-publisher
 * Requires at least: 6.0
 * Tested up to: 6.8.2
 * Requires PHP: 8.2
 * Stable tag: 1.1.1
 */

use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';

WordpressPlugin::init(__FILE__);

