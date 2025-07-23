<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Adapter\WordpressPlugin;

final class WordpressHookRegistrar
{
    public function register(string $pluginFile): void
    {
        add_action('init', [WordpressPlugin::class, 'boot']);
        register_activation_hook($pluginFile, [WordpressPlugin::class, 'onActivate']);
        register_uninstall_hook($pluginFile, [WordpressPlugin::class, 'onUninstall']);
        add_action('admin_notices', [WordpressPlugin::class, 'showAdminNotice']);
        add_action('admin_post_xpub_publish', [WordpressPlugin::class, 'handlePublishFromPost']);
    }
}
