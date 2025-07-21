<?php

declare(strict_types=1);

namespace N3XT0R\XPub;

use N3XT0R\XPub\Core\PublisherFactory;
use N3XT0R\XPub\Setup\SetupRunner;
use N3XT0R\XPub\Support\Version;

/**
 * Main entry point for the WP-XPub plugin lifecycle (activation, boot, uninstall).
 */
final class Plugin
{
    private static string $pluginFile;

    public static function init(string $pluginFile): void
    {
        self::$pluginFile = $pluginFile;

        add_action('init', [self::class, 'boot']);
        register_activation_hook(self::$pluginFile, [self::class, 'onActivate']);
        register_uninstall_hook(self::$pluginFile, [self::class, 'onUninstall']);

        add_action('admin_notices', [self::class, 'showAdminNotice']);
    }

    public static function boot(): void
    {
        $currentVersion = Version::get();
        $savedVersion = get_option('xpub_plugin_version');

        if (!empty($currentVersion) && version_compare($currentVersion, (string)$savedVersion, '>')) {
            (new SetupRunner())->install();
            update_option('xpub_plugin_version', $currentVersion);
        }

        // Optional: Nur wenn gewollt
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

    public static function showAdminNotice(): void
    {
        if (!is_admin()) {
            return;
        }

        if ($msg = get_option('xpub_admin_notice')) {
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($msg).'</p></div>';
            delete_option('xpub_admin_notice');
        }
    }
}
