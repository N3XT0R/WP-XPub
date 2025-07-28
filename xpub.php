<?php

include_once 'version.php';

/**
 * Plugin Name: WP-XPub
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 1.0.0
 * Author: Ilya Beliaev (N3XT0R)
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 */


use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
