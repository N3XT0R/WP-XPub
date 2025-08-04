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
            __('XPUB Einstellungen', 'xpub-multi-channel-publisher'),
            'XPUB',
            'manage_options',
            'xpub-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void
    {
        View::render('layouts.admin', [
            'title' => __('XPUB Einstellungen', 'xpub-multi-channel-publisher'),
            'content' => fn() => View::render('admin.react-settings-page', $this->service->getSettingsViewData()),
        ]);
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'settings_page_xpub-settings') {
            return;
        }

        $this->enqueueWordPressDependencies();

        if ($this->isDevelopmentEnvironment() && 1 === 2) {
            $this->injectViteDevScripts();
        } else {
            $this->enqueueProductionAssets();
        }
    }

    private function enqueueWordPressDependencies(): void
    {
        wp_enqueue_script('wp-element');
        wp_enqueue_script('wp-i18n');
    }

    private function isDevelopmentEnvironment(): bool
    {
        $env = function_exists('wp_get_environment_type')
            ? wp_get_environment_type()
            : 'production';

        return in_array($env, ['local', 'development'], true);
    }

    private function injectViteDevScripts(): void
    {
        add_action('admin_head', function () {
            echo <<<HTML
                <script type="module" src="http://localhost:5173/@vite/client"></script>
                <script type="module">
                    import RefreshRuntime from "http://localhost:5173/@react-refresh";
                    RefreshRuntime.injectIntoGlobalHook(window);
                    window.\$RefreshReg\$ = () => {};
                    window.\$RefreshSig\$ = () => (type) => type;
                    window.__vite_plugin_react_preamble_installed__ = true;
                </script>
                <script type="module" src="http://localhost:5173/main.jsx"></script>
            HTML;
        });
    }

    private function enqueueProductionAssets(): void
    {
        $manifest = $this->loadViteManifest();
        if (!$manifest) {
            return;
        }

        $entry = $manifest['main.jsx'] ?? reset($manifest);
        if (!isset($entry['file'])) {
            return;
        }

        $baseUrl = plugins_url('dist/', $this->pluginContext->pluginFile);
        $basePath = plugin_dir_path($this->pluginContext->pluginFile);
        $handle = 'xpub-settings-app';

        wp_enqueue_script(
            $handle,
            $baseUrl.$entry['file'],
            ['wp-i18n', 'wp-element'],
            null,
            true
        );

        add_filter('script_loader_tag', function ($tag, $handleFromFilter, $src) use ($handle) {
            if ($handleFromFilter === $handle) {
                return '<script type="module" src="'.esc_url($src).'"></script>';
            }
            return $tag;
        }, 10, 3);

        wp_set_script_translations(
            $handle,
            'xpub-multi-channel-publisher',
            $basePath.'languages'
        );
        

        if (!empty($entry['css'][0])) {
            wp_enqueue_style(
                'xpub-settings-style',
                $baseUrl.$entry['css'][0],
            );
        }
    }

    private function loadViteManifest(): ?array
    {
        $path = plugin_dir_path($this->pluginContext->pluginFile).'dist/.vite/manifest.json';

        if (!file_exists($path)) {
            return null;
        }

        $json = file_get_contents($path);
        $manifest = json_decode((string)$json, true);

        return is_array($manifest) ? $manifest : null;
    }

}

