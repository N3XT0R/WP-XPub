<?php
/**
 * Plugin Name: XPub Multi-Channel Publisher
 * Description: Flexible Multi-Channel Auto Publisher for WordPress
 * Version: 1.5.0-dev
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

use N3XT0R\XPub\Adapter\WordpressCron;
use N3XT0R\XPub\Adapter\WordpressPlugin;
use N3XT0R\XPub\Infrastructure\DI\ContainerProvider;
use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactory;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

defined('ABSPATH') or die('No script kiddies please!');
require_once __DIR__.'/vendor/autoload.php';

ContainerProvider::setPluginContext(
    new PluginContext(
        __FILE__,
        'xpub-multi-channel-publisher',
        'https://github.com/N3XT0R/WP-XPub',
        'xpub'
    )
);

$container = ContainerProvider::getContainer();
WordpressPlugin::setContainer($container);
WordpressCron::setContainer($container);
PublisherFactory::setContainer($container);

WordpressPlugin::init(__FILE__);

