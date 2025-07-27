<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Infrastructure\Wordpress\Factory\WordpressPublisherFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Mapper\ArticleMapper;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use WP_Post;

/**
 * Main entry point for the WP-XPub plugin lifecycle (activation, boot, uninstall).
 */
final class WordpressPlugin
{

    /**
     * Initializes the plugin and registers hooks.
     */
    public static function init(string $pluginFile): void
    {
        $registrar = new WordpressHookRegistrar();
        $registrar->register($pluginFile);
    }

    /**
     * Bootstraps the plugin (e.g., version checks, setup, etc.).
     */
    public static function boot(): void
    {
        $bootstrapper = new PluginBootstrapService(new WordpressSettingsRepository());
        $bootstrapper->bootstrap();
    }

    /**
     * Displays admin notices in the WP backend.
     */
    public static function showAdminNotice(): void
    {
        $presenter = new AdminNoticePresenter(new WordpressSettingsRepository());
        $presenter->showIfAvailable();
    }

    public static function handlePublishFromPost(int $postId, WP_Post $post): void
    {
        self::handleSaveFromPost($postId, $post, true);
    }


    public static function handleSaveFromPost(int $postId, WP_Post $post, bool $update = false): void
    {
        if ($update || $post->post_status !== 'publish') {
            return;
        }

        $article = ArticleMapper::fromPost($post);
        $publisher = WordpressPublisherFactory::create();
        $publisher->publish($article);
    }

}
