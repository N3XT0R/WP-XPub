<?php
/*
Plugin Name: xPub
Description: Modular Auto-Publisher for WordPress to external platforms (dev.to, LinkedIn, etc.)
Version: 0.2
Author: php-dev
*/

defined('ABSPATH') or die('No script kiddies please!');

require_once __DIR__ . '/vendor/autoload.php';

use XPub\Core\PublisherFactory;

add_action('init', function () {
    $publisher = PublisherFactory::create('devto');
    $publisher->publish('Hello World', 'Dies ist ein Testbeitrag.');
});
