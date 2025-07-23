<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Adapter\WordpressPlugin;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;

final class WordpressHookRegistrar
{
    public function register(string $pluginFile): void
    {
        add_action('init', [WordpressPlugin::class, 'boot']);
        register_activation_hook($pluginFile, [WordpressPlugin::class, 'onActivate']);
        register_uninstall_hook($pluginFile, [WordpressPlugin::class, 'onUninstall']);
        add_action('admin_notices', [WordpressPlugin::class, 'showAdminNotice']);
        add_action('save_post', [WordpressPlugin::class, 'handleSaveFromPost'], 10, 2);
        add_action('publish_post', [WordpressPlugin::class, 'handlePublishFromPost'], 10, 2);
        add_action('admin_menu', [SettingsPageRegistrar::class, 'addOptionsPage']);
    }
}
