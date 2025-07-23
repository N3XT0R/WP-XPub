<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;

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
        $bootstrapper = new PluginBootstrapService();
        $bootstrapper->bootstrap();
    }

    /**
     * Runs on plugin activation (e.g., DB setup).
     */
    public static function onActivate(): void
    {
        (new SetupRunner())->install();
    }

    /**
     * Runs on plugin uninstall (e.g., cleanup).
     */
    public static function onUninstall(): void
    {
        (new SetupRunner())->uninstall();
    }

    /**
     * Displays admin notices in the WP backend.
     */
    public static function showAdminNotice(): void
    {
        $presenter = new AdminNoticePresenter();
        $presenter->showIfAvailable();
    }


    public static function handlePublishFromPost(int $postId, \WP_Post $post, bool $update): void
    {
        if ($update || $post->post_status !== 'publish') {
            return;
        }

        $title = $post->post_title;
        $content = $post->post_content;

        $article = new Article($title, $content);
        $publisher = new ArticlePublisher(new DevToPublisher());

        $publisher->publish($article);
    }

}
