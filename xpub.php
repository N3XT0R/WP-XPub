<?php
/**
 * Plugin Name: WP-XPub
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 1.0.0-RC1
 * Author: Ilya Beliaev
 * License: GPLV3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 * Text Domain: xpub
 * Requires at least: 6.0
 * Tested up to: 6.8.2
 * Requires PHP: 8.2
 * Stable tag: 1.0.0-RC1
 */


include_once 'version.php';

use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
