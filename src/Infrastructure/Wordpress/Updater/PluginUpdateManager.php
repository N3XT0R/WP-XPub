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
            $pluginFile,
            'xpub-multi-channel-publisher',                                   // slug
            'https://github.com/N3XT0R/WP-XPub/latest/xpub-multi-channel-publisher.zip',
            'https://github.com/N3XT0R/WP-XPub'          // optional info URL
        ))->register();
    }

    public function register(): void
    {
        add_filter('site_transient_update_plugins', [$this, 'removeWpOrgUpdate']);
        add_filter('pre_set_site_transient_update_plugins', [$this, 'injectUpdateMetadata']);
    }

    public function removeWpOrgUpdate($transient)
    {
        unset($transient->response[$this->pluginFile]);
        return $transient;
    }

    public function injectUpdateMetadata($transient)
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $pluginPath = WP_PLUGIN_DIR.'/'.$this->pluginFile;
        $pluginData = get_plugin_data($pluginPath, false, false);
        $currentVersion = $pluginData['Version'];
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
        // Beispiel: lies Version aus GitHub Release JSON oder eigener API
        $json = @file_get_contents('https://raw.githubusercontent.com/N3XT0R/WP-XPub/master/version.json');

        if ($json !== false) {
            $data = json_decode($json, true);
            return $data['version'] ?? '0.0.0';
        }

        return '0.0.0';
    }
}
