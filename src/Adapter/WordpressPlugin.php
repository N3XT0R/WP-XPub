<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Application\Service\Queue\AsyncPublishingDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Infrastructure\Wordpress\Service\Plugin\PluginBootstrapService;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use N3XT0R\XPub\Infrastructure\DI\ContainerProvider;
use DI\Container;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use WP_Post;

/**
 * Main entry point for the WP-XPub plugin lifecycle (activation, boot, uninstall).
 * @codeCoverageIgnore
 */
final class WordpressPlugin
{
    private static ?Container $container = null;

    private static function container(): Container
    {
        if (self::$container === null) {
            self::$container = ContainerProvider::getContainer();
        }

        return self::$container;
    }

    /**
     * Initializes the plugin and registers hooks.
     */
    public static function init(string $pluginFile): void
    {
        self::container();
        View::setBasePath(plugin_dir_path(__FILE__).'../../resources/views');
        PublisherFactory::setFilterDispatcher(
            self::container()->get(FilterDispatcherInterface::class)
        );
        $updateManager = self::container()->make(PluginUpdateManager::class, [
            'pluginFile' => plugin_basename($pluginFile),
            'pluginSlug' => 'xpub-multi-channel-publisher',
            'pluginInfoUrl' => 'https://github.com/N3XT0R/WP-XPub',
        ]);

        $hookProvider = self::container()->make(HookProvider::class, [
            'saveHandler' => self::container()->get(SettingsSaveHandler::class),
            'oauthController' => self::container()->get(OAuthController::class),
            'updateManager' => $updateManager,
        ]);

        $registrar = new WordpressHookRegistrar(
            $hookProvider,
            self::container()->get(HookDispatcherInterface::class),
            [
                self::container()->get(SettingsPageRegistrar::class),
                self::container()->get(MetaBox::class),
            ]
        );
        $registrar->register($pluginFile);
    }

    /**
     * Bootstraps the plugin (e.g., version checks, setup, etc.).
     */
    public static function boot(): void
    {
        self::container()
            ->get(PluginBootstrapService::class)
            ->bootstrap();
    }

    /**
     * Runs on plugin activation (e.g., DB setup).
     */
    public static function onActivate(string $channel = 'xpub'): void
    {
        self::container()
            ->make(SetupRunner::class, ['logger' => LoggerFactory::create($channel)])
            ->install();
        WordpressCron::schedule();
    }

    /**
     * Runs on plugin uninstall (e.g., cleanup).
     */
    public static function onUninstall(string $channel = 'xpub'): void
    {
        self::container()
            ->make(SetupRunner::class, ['logger' => LoggerFactory::create($channel)])
            ->uninstall();
        WordpressCron::unschedule();
    }

    /**
     * Displays admin notices in the WP backend.
     */
    public static function showAdminNotice(): void
    {
        self::container()
            ->get(AdminNoticePresenter::class)
            ->showIfAvailable();
    }

    public static function handlePublishFromPost(int $postId, ?WP_Post $post): void
    {
        self::handleSaveFromPost($postId, $post);
    }


    public static function handleSaveFromPost(int $postId, ?WP_Post $post): void
    {
        if (!$post) {
            return;
        }

        $dispatcher = self::getDispatcher();
        $article = self::container()
            ->get(ArticleFactory::class)
            ->fromWpPost($post);
        $dispatcher->dispatch($article);
    }

    private static function getDispatcher(): AsyncPublishingDispatcher
    {
        return self::container()->get(AsyncPublishingDispatcher::class);
    }

}
