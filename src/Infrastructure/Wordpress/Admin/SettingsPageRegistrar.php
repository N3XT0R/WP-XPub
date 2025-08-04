<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookRegistrableInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

final class SettingsPageRegistrar implements HookRegistrableInterface
{
    public function __construct(
        private PublisherSettingsService $service,
        private PluginContext $pluginContext
    ) {
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addOptionsPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addOptionsPage(): void
    {
        add_options_page(
            'XPUB Einstellungen',
            'XPUB',
            'manage_options',
            'xpub-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void
    {
        View::render('layouts.admin', [
            'title' => 'XPUB Einstellungen',
            'content' => fn() => View::render('admin.react-settings-page', $this->service->getSettingsViewData()),
        ]);
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'settings_page_xpub-settings') {
            return;
        }

        $handle = 'xpub-settings-app';

        $env = function_exists('wp_get_environment_type')
            ? wp_get_environment_type()
            : 'production';

        if (in_array($env, ['local', 'development'], true)) {
            add_action('admin_head', function () {
                echo '<script type="module" src="http://localhost:5173/@vite/client"></script>'.PHP_EOL;
                echo '<script type="module">
                        import RefreshRuntime from "http://localhost:5173/@react-refresh";
                        RefreshRuntime.injectIntoGlobalHook(window);
                        window.$RefreshReg$ = () => {};
                        window.$RefreshSig$ = () => (type) => type;
                        window.__vite_plugin_react_preamble_installed__ = true;
                    </script>'.PHP_EOL;
                echo '<script type="module" src="http://localhost:5173/main.jsx"></script>'.PHP_EOL;
            });

            return;
        }

        // Prod-Modus: Manifest auslesen und Skripte regulär einbinden
        $manifestPath = plugin_dir_path($this->pluginContext->pluginFile).'dist/.vite/manifest.json';
        if (!file_exists($manifestPath)) {
            return;
        }

        $manifest = json_decode((string)file_get_contents($manifestPath), true);
        if (!is_array($manifest) || $manifest === []) {
            return;
        }

        $entry = reset($manifest);
        if (!is_array($entry) || !isset($entry['file'])) {
            return;
        }

        $baseUrl = plugins_url('dist/', $this->pluginContext->pluginFile);

        wp_enqueue_script(
            $handle,
            $baseUrl.$entry['file'],
            ['wp-i18n', 'wp-element']
        );

        if (!empty($entry['css'][0])) {
            wp_enqueue_style(
                'xpub-settings-style',
                $baseUrl.$entry['css'][0],
                ['wp-i18n', 'wp-element']
            );
        }
    }

}

