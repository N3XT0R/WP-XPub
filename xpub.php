<?php

include_once 'version.php';

/**
 * Plugin Name: WP-XPub
 * Plugin URI: https://github.com/N3XT0R/wp-xpub
 * Description: Flexible multi-channel auto-publishing plugin using hexagonal architecture. Publish WordPress posts to external platforms like Mastodon, LinkedIn, Dev.to and more.
 * Version: 1.0.0
 * Author: Ilya Beliaev (N3XT0R)
 * Author URI: https://github.com/N3XT0R
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-xpub
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.8.2
 * Requires PHP: 8.2
 * Stable tag: 1.0.0
 */


use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
