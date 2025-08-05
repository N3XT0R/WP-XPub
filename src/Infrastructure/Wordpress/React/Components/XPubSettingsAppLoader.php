<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\React\Components;

use N3XT0R\XPub\Infrastructure\Wordpress\React\ReactAppLoader;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

final class XPubSettingsAppLoader
{
    public function __construct(
        private PluginContext $pluginContext
    ) {
    }

    public function register(): void
    {
        $loader = new ReactAppLoader(
            pluginContext: $this->pluginContext,
            scriptName: 'main.jsx',
            jsVarName: 'xpubSettings',
            dataToInject: [
                'locale' => get_user_locale(),
                'translationsBaseUrl' => plugins_url('frontend/translations', $this->pluginContext->pluginFile),
                'restUrl' => rest_url(),
                'restNonce' => wp_create_nonce('wp_rest'),
            ]
        );

        $loader->load();
    }
}