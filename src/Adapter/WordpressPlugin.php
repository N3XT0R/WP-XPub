<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\AsyncPublishingDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WPDBQueueRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use WP_Post;

/**
 * Main entry point for the WP-XPub plugin lifecycle (activation, boot, uninstall).
 * @codeCoverageIgnore
 */
final class WordpressPlugin
{

    /**
     * Initializes the plugin and registers hooks.
     */
    public static function init(string $pluginFile): void
    {
        View::setBasePath(plugin_dir_path(__FILE__).'../../resources/views');
        PublisherFactory::setFilterDispatcher(new WordpressFilterDispatcher());
        $registrar = new WordpressHookRegistrar(new HookProvider($pluginFile));
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
     * Runs on plugin activation (e.g., DB setup).
     */
    public static function onActivate(string $channel = 'xpub'): void
    {
        $runner = new SetupRunner(
            LoggerFactory::create($channel),
            new WordpressSettingsRepository()
        );
        $runner->install();
        WordpressCron::register();
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
        WordpressCron::deregister();
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

        $dispatcher = self::getDispatcher();
        $article = (new ArticleFactory(new WpPostContentRenderer()))->fromWpPost($post);
        $dispatcher->dispatch($article);
    }

    private static function getDispatcher(): AsyncPublishingDispatcher
    {
        return new AsyncPublishingDispatcher(
            new WPDBQueueRepository(Database::get()),
            new PublisherSelector(new PublisherRepository(), new PublisherFactory()),
            new ArticleFactory(new WpPostContentRenderer())
        );
    }

}
