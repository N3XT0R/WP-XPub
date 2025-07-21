<?php

declare(strict_types=1);

namespace N3XT0R\XPub;

use N3XT0R\XPub\Core\PublisherFactory;
use N3XT0R\XPub\Setup\SetupRunner;
use N3XT0R\XPub\Support\Version;

final class Plugin
{
    public static function init(): void
    {
        add_action('init', [self::class, 'boot']);
        register_activation_hook(self::getPluginFile(), [self::class, 'onActivate']);
        register_uninstall_hook(self::getPluginFile(), [self::class, 'onUninstall']);
    }

    public static function boot(): void
    {
        $currentVersion = Version::get();
        $savedVersion = get_option('xpub_plugin_version');

        if (!empty($currentVersion) && version_compare($currentVersion, (string)$savedVersion, '>')) {
            (new SetupRunner())->install();
            update_option('xpub_plugin_version', $currentVersion);
        }

        $publisher = PublisherFactory::create('devto');
        $publisher->publish('Hello World', 'Dies ist ein Testbeitrag.');
    }

    public static function onActivate(): void
    {
        (new SetupRunner())->install();
    }

    public static function onUninstall(): void
    {
        (new SetupRunner())->uninstall();
    }

    private static function getPluginFile(): string
    {
        return WP_PLUGIN_DIR.'/xpub/xpub.php';
    }
}
