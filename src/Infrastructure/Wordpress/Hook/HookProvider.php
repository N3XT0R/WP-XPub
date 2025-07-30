<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Adapter\WordpressPlugin;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;

final class HookProvider
{
    public function __construct(
        private readonly string $pluginFile,
        private readonly SettingsSaveHandler $saveHandler,
        private readonly OAuthController $oauthController,
    ) {
    }

    /**
     * @return HookDefinition[]
     */
    public function getHooks(): array
    {
        return [
            new HookDefinition('init', [WordpressPlugin::class, 'boot']),
            new HookDefinition('admin_notices', [WordpressPlugin::class, 'showAdminNotice']),
            new HookDefinition('save_post', [WordpressPlugin::class, 'handleSaveFromPost'], 10, 2),
            new HookDefinition('publish_post', [WordpressPlugin::class, 'handlePublishFromPost'], 10, 2),
            new HookDefinition('admin_post_xpub_save_settings', fn() => $this->saveHandler->handle()),
            new HookDefinition('init', fn() => PluginUpdateManager::boot($this->pluginFile)),
            new HookDefinition('rest_api_init', [$this->oauthController, 'register']),
        ];
    }
}
