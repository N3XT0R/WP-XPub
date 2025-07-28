<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Updater;

use function get_plugin_data;

class PluginUpdateManager
{
    private string $pluginFile;
    private string $pluginSlug;
    private string $updatePackageUrl;
    private string $pluginInfoUrl;

    public function __construct(
        string $pluginFile,
        string $pluginSlug,
        string $updatePackageUrl,
        string $pluginInfoUrl = ''
    ) {
        $this->pluginFile = $pluginFile;
        $this->pluginSlug = $pluginSlug;
        $this->updatePackageUrl = $updatePackageUrl;
        $this->pluginInfoUrl = $pluginInfoUrl;
    }

    public static function boot(string $pluginFile): void
    {
        (new PluginUpdateManager(
            plugin_basename($pluginFile),
            'xpub-multi-channel-publisher',                                   // slug
            'https://github.com/N3XT0R/WP-XPub/latest/xpub-multi-channel-publisher.zip',
            'https://github.com/N3XT0R/WP-XPub'          // optional info URL
        ))->register();
    }

    public function register(): void
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'injectUpdateMetadata']);
        add_filter('plugin_action_links_'.$this->pluginFile, [$this, 'addManualUpdateLink']);
        add_filter('plugins_api', [$this, 'injectPluginDetails'], 10, 3);
        add_action('admin_init', [$this, 'handleManualUpdateRequest']);
        add_action('admin_notices', [$this, 'showUpdateNotice']);
    }

    public function addManualUpdateLink(array $actions): array
    {
        $url = admin_url('plugins.php?manual_xpub_update_check=1');
        $actions['manual_update_check'] = '<a href="'.esc_url($url).'">'.esc_html__(
                'Check for updates manually',
                'default'
            ).'</a>';

        return $actions;
    }

    public function handleManualUpdateRequest(): void
    {
        if (isset($_GET['manual_xpub_update_check'])) {
            delete_site_transient('update_plugins');
            wp_update_plugins(); // Löst deine Hooks aus
            wp_safe_redirect(admin_url('plugins.php?xpub_update_checked=1'));
            exit;
        }
    }

    public function showUpdateNotice(): void
    {
        if (isset($_GET['xpub_update_checked'])) {
            echo '<div class="notice notice-success is-dismissible"><p>'.
                esc_html__('Plugins list updated.', 'default').
                '</p></div>';
        }
    }

    public function injectUpdateMetadata($transient)
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $pluginPath = WP_PLUGIN_DIR.'/'.$this->pluginFile;
        $pluginData = get_plugin_data($pluginPath, false, false);
        $rawVersion = $pluginData['Version'] ?? '';

        if (preg_match('/^\d+\.\d+\.\d+(?:-[\w\.]+)?$/', $rawVersion)) {
            $currentVersion = $rawVersion;
        } else {
            $currentVersion = '0.0.0';
        }
        $remoteVersion = $this->getRemoteVersion();
        if (version_compare($remoteVersion, $currentVersion, '>')) {
            $transient->response[$this->pluginFile] = (object)[
                'slug' => $this->pluginSlug,
                'plugin' => $this->pluginFile,
                'new_version' => $remoteVersion,
                'url' => $this->pluginInfoUrl,
                'package' => $this->updatePackageUrl,
            ];
        }

        return $transient;
    }

    private function getRemoteVersion(): string
    {
        $json = @file_get_contents('https://raw.githubusercontent.com/N3XT0R/WP-XPub/master/version.json');

        if ($json !== false) {
            $data = json_decode($json, true);
            return $data['version'] ?? '0.0.0';
        }

        return '0.0.0';
    }

    public function injectPluginDetails($result, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->pluginSlug) {
            return $result;
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $pluginPath = WP_PLUGIN_DIR.'/'.$this->pluginFile;
        $pluginData = get_plugin_data($pluginPath, false, false);

        $result = (object)[
            'name' => $pluginData['Name'] ?? '',
            'slug' => $this->pluginSlug,
            'version' => $pluginData['Version'] ?? '',
            'author' => $pluginData['Author'] ?? '',
            'homepage' => $this->pluginInfoUrl,
            'requires' => $pluginData['RequiresWP'] ?? '6.0',
            'tested' => $pluginData['TestedUpTo'] ?? get_bloginfo('version'),
            'download_link' => $this->updatePackageUrl,
            'requires_php' => $pluginData['RequiresPHP'] ?? '7.4',
            'sections' => [
                'description' => $pluginData['Description'] ?? '',
                'changelog' => $this->getChangelogHtml(),
            ],
        ];

        return $result;
    }

    private function getChangelogHtml(): string
    {
        $changelogPath = plugin_dir_path(WP_PLUGIN_DIR.'/'.$this->pluginFile).'CHANGELOG.md';

        if (!file_exists($changelogPath)) {
            return '<p>-</p>';
        }

        $content = file_get_contents($changelogPath);
        $content = esc_html($content);
        $content = nl2br($content);
        $content = preg_replace('/#+\s*(.*?)\s*<br\s*\/?>/', '<h3>$1</h3>', $content);

        return $content;
    }


}
