<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\React;

use N3XT0R\XPub\Shared\Plugin\PluginContext;

final class ReactAppLoader
{
    public function __construct(
        private PluginContext $pluginContext,
        private string $scriptName,               // e.g. main.jsx
        private string $jsVarName,                // e.g. xpubSettings
        private array $dataToInject               // e.g. ['locale' => ..., 'restUrl' => ...]
    )
    {
    }

    public function load(): void
    {
        if ($this->isDevelopmentEnvironment()) {
            $this->injectDevScripts();
        } else {
            $this->injectProductionAssets();
        }
    }

    private function isDevelopmentEnvironment(): bool
    {
        $env = function_exists('wp_get_environment_type')
            ? wp_get_environment_type()
            : 'production';

        return in_array($env, ['local', 'development'], true);
    }

    private function injectDevScripts(): void
    {
        add_action('admin_head', function () {
            $dataJs = json_encode($this->dataToInject, JSON_UNESCAPED_SLASHES);
            $var = $this->jsVarName;
            $script = <<<HTML
                <script type="module" src="http://localhost:5173/@vite/client"></script>
                <script type="module">
                    import RefreshRuntime from "http://localhost:5173/@react-refresh";
                    RefreshRuntime.injectIntoGlobalHook(window);
                    window.\$RefreshReg\$ = () => {};
                    window.\$RefreshSig\$ = () => (type) => type;
                    window.__vite_plugin_react_preamble_installed__ = true;
                </script>
                <script type="module" src="http://localhost:5173/{$this->scriptName}"></script>
                <script type="module">window.{$var} = {$dataJs};</script>
            HTML;

            echo $script;
        });
    }

    private function injectProductionAssets(): void
    {
        $manifest = $this->loadViteManifest();
        if (!$manifest) {
            return;
        }

        $entry = $manifest[$this->scriptName] ?? reset($manifest);
        if (!isset($entry['file'])) {
            return;
        }

        $baseUrl = plugins_url('dist/', $this->pluginContext->pluginFile);
        $handle = 'xpub-'.$this->jsVarName;

        wp_enqueue_script(
            $handle,
            $baseUrl.$entry['file'],
            ['wp-element', 'wp-i18n'],
            null,
            true
        );

        add_filter('script_loader_tag', function ($tag, $handleFromFilter, $src) use ($handle) {
            if ($handleFromFilter === $handle) {
                return '<script type="module" src="'.esc_url($src).'"></script>';
            }
            return $tag;
        }, 10, 3);

        add_action('admin_head', function () {
            echo '<script type="module">window.'.$this->jsVarName.' = '.json_encode(
                    $this->dataToInject,
                    JSON_UNESCAPED_SLASHES
                ).';</script>';
        });

        if (!empty($entry['css'][0])) {
            wp_enqueue_style(
                $handle.'-style',
                $baseUrl.$entry['css'][0]
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
