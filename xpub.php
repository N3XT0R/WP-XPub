<?php
/*
Plugin Name: xPub
Description: Modular Auto-Publisher for WordPress to external platforms (dev.to, LinkedIn, etc.)
Author: php-dev
*/

defined('ABSPATH') or die('No script kiddies please!');

require_once __DIR__.'/vendor/autoload.php';

use N3XT0R\XPub\Core\PublisherFactory;
use N3XT0R\XPub\Setup\SetupRunner;

add_action('init', function () {
    $publisher = PublisherFactory::create('devto');
    $publisher->publish('Hello World', 'Dies ist ein Testbeitrag.');
});

register_activation_hook(__FILE__, function () {
    (new SetupRunner())->executeInstall();
});

register_uninstall_hook(__FILE__, function () {
    (new SetupRunner())->uninstall();
});
