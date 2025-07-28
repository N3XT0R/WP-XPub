<?php
/**
 * Plugin Name: XPub Multi-Channel Publisher
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 0.1.0
 * Author: Ilya Beliaev
 * License: GPL-3.0-OR-LATER
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 * Text Domain: xpub-multi-channel-publisher
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 8.2
 * Stable tag: 0.1.0
 */


include_once 'version.php';

use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
