<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Application\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Application\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
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
     * Runs on plugin activation (e.g., DB setup).
     */
    public static function onActivate(string $channel = 'xpub'): void
    {
        $runner = new SetupRunner(LoggerFactory::create($channel), new WordpressSettingsRepository());
        $runner->install();
    }

    /**
     * Runs on plugin uninstall (e.g., cleanup).
     */
    public static function onUninstall(string $channel = 'xpub'): void
    {
        $runner = new SetupRunner(LoggerFactory::create($channel), new WordpressSettingsRepository());
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

    private static function createArticleFromPost(WP_Post $post): Article
    {
        return new Article($post->ID, $post->post_title, $post->post_content);
    }

    private static function createPublisher(): ArticlePublisher
    {
        $repository = new PublisherRepository();
        $allPublishers = $repository->all();

        $provider = new PublisherTargetProvider(new WordpressSettingsRepository());
        $activeTargets = $provider->getTargets(); // e.g. ['devto', 'hashnode']

        $instances = [];

        foreach ($allPublishers as $publisher) {
            if (in_array($publisher->getSlug(), $activeTargets, true)) {
                try {
                    $instances[] = PublisherFactory::createWithConfig(
                        $publisher->getSlug(),
                        $publisher->getConfigArray()
                    );
                } catch (\RuntimeException $e) {
                    LoggerFactory::create()->error($e->getMessage(), ['exception' => $e]);
                }
            }
        }

        return new ArticlePublisher($instances);
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

        $article = self::createArticleFromPost($post);
        $publisher = self::createPublisher();
        $publisher->publish($article);
    }
    
}
