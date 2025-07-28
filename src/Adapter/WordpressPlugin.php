<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\WordpressPublisherFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
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
        PublisherFactory::setFilterDispatcher(new WordpressFilterDispatcher());
        $registrar = new WordpressHookRegistrar();
        $registrar->register($pluginFile);
        Translator::register($pluginFile);
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
     * Runs on plugin activation (e.g., DB setup).
     */
    public static function onActivate(string $channel = 'xpub'): void
    {
        error_log('INSTALL XPub gestartet');
        $runner = new SetupRunner(
            LoggerFactory::create($channel),
            new WordpressSettingsRepository()
        );
        $runner->install();
    }

    /**
     * Runs on plugin uninstall (e.g., cleanup).
     */
    public static function onUninstall(string $channel = 'xpub'): void
    {
        $runner = new SetupRunner(
            LoggerFactory::create($channel),
            new WordpressSettingsRepository()
        );
        $runner->uninstall();
    }

    /**
     * Displays admin notices in the WP backend.
     */
    public static function showAdminNotice(): void
    {
        $presenter = new AdminNoticePresenter(new WordpressSettingsRepository());
        $presenter->showIfAvailable();
    }

    public static function handlePublishFromPost(int $postId, ?WP_Post $post): void
    {
        self::handleSaveFromPost($postId, $post, true);
    }


    public static function handleSaveFromPost(int $postId, ?WP_Post $post, bool $update = false): void
    {
        if (!$post) {
            return;
        }


        if ($update || $post->post_status !== 'publish') {
            return;
        }

        $article = (new ArticleFactory(new WpPostContentRenderer()))->fromWpPost($post);
        $publisher = WordpressPublisherFactory::create();
        $publisher->publish($article);
    }

}
