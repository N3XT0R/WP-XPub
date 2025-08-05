<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Updater;

use N3XT0R\XPub\Domain\Contracts\ClearContainerCacheInterface;
use N3XT0R\XPub\Domain\Contracts\ReleaseProviderInterface;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

use function get_plugin_data;

class PluginUpdateManager
{
    private string $pluginFile;
    private string $pluginSlug;
    private string $pluginInfoUrl;
    private ReleaseProviderInterface $releaseService;

    private ClearContainerCacheInterface $clearContainerCache;

    public function __construct(
        PluginContext $pluginContext,
        ReleaseProviderInterface $releaseService,
        ClearContainerCacheInterface $clearContainerCache,
    ) {
        $this->pluginFile = $pluginContext->pluginFile;
        $this->pluginSlug = $pluginContext->pluginSlug;
        $this->pluginInfoUrl = $pluginContext->pluginInfoUrl;
        $this->releaseService = $releaseService;
        $this->clearContainerCache = $clearContainerCache;
    }

    public function register(): void
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'injectUpdateMetadata']);
        add_filter('plugin_action_links_'.$this->pluginFile, [$this, 'addManualUpdateLink']);
        add_filter('plugins_api', [$this, 'injectPluginDetails'], 10, 3);
        add_action('admin_init', [$this, 'handleManualUpdateRequest']);
        add_action('admin_notices', [$this, 'showUpdateNotice']);
    }

    public function increaseDownloadTimeout(array $args, string $url): array
    {
        if (str_contains($url, 'github.com')) {
            $args['timeout'] = 300;
        }

        return $args;
    }

    public function addManualUpdateLink(array $actions): array
    {
        $url = admin_url('plugins.php?manual_xpub_update_check=1');
        $actions['manual_update_check'] = '<a href="'.esc_url($url).'">'.
            esc_html__('Check for updates manually', 'default').'</a>';

        return $actions;
    }

    public function handleManualUpdateRequest(): void
    {
        if (isset($_GET['manual_xpub_update_check'])) {
            delete_site_transient('update_plugins');
            wp_update_plugins();
            $this->clearContainerCache->clear();
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

        $pluginFile = plugin_basename($this->pluginFile);
        $pluginPath = WP_PLUGIN_DIR.'/'.plugin_basename($pluginFile);
        $pluginData = get_plugin_data($pluginPath, false, false);
        $currentVersion = $pluginData['Version'] ?? '0.0.0';

        $release = $this->releaseService->fetchLatestRelease();
        if ($release && version_compare($release['version'], $currentVersion, '>')) {
            $transient->response[$pluginFile] = (object)[
                'slug' => $this->pluginSlug,
                'plugin' => $pluginFile,
                'new_version' => $release['version'],
                'url' => $this->pluginInfoUrl,
                'package' => $release['download_url'],
            ];
        }

        return $transient;
    }

    public function injectPluginDetails($result, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->pluginSlug) {
            return $result;
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $pluginPath = WP_PLUGIN_DIR.'/'.plugin_basename($this->pluginFile);
        $pluginData = get_plugin_data($pluginPath, false, false);

        $release = $this->releaseService->fetchLatestRelease();

        return (object)[
            'name' => $pluginData['Name'] ?? '',
            'slug' => $this->pluginSlug,
            'version' => $pluginData['Version'] ?? '',
            'author' => $pluginData['Author'] ?? '',
            'homepage' => $this->pluginInfoUrl,
            'requires' => $pluginData['RequiresWP'] ?? '6.0',
            'tested' => $pluginData['TestedUpTo'] ?? get_bloginfo('version'),
            'download_link' => $release['download_url'] ?? '',
            'requires_php' => $pluginData['RequiresPHP'] ?? '8.2',
            'sections' => [
                'description' => $pluginData['Description'] ?? '',
                'changelog' => $this->formatMarkdown($release['changelog'] ?? ''),
            ],
        ];
    }

    private function formatMarkdown(string $markdown): string
    {
        $content = esc_html($markdown);
        $content = nl2br($content);
        $content = preg_replace('/#+\s*(.*?)\s*<br\s*\/?>/', '<h3>$1</h3>', $content);

        return $content;
    }
}
