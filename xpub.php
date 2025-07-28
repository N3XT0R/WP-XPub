<?php

include_once 'version.php';

/**
 * Plugin Name: xPub-Publisher (WP-XPub)
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 1.0.0
 * Author: N3XT0R
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */


use N3XT0R\XPub\Adapter\WordpressPlugin;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';


WordpressPlugin::init(__FILE__);
