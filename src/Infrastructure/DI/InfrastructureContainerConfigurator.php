<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\DI;

use DI\ContainerBuilder;
use N3XT0R\XPub\Application\Cache\ClearContainerCacheInterface;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\Factory\WordpressArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\HtmlToMarkdownRendererInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Domain\Contracts\TranslatesMessagesInterface;
use N3XT0R\XPub\Domain\Contracts\ViewInterface;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\DI\Cache\ContainerCacheCleaner;
use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRendererFactory;
use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactoryService;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\React\Components\XPubSettingsAppLoader;
use N3XT0R\XPub\Infrastructure\Wordpress\React\ReactAppLoader;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WPDBQueueRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WpPostStatusRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\SettingsController;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;
use N3XT0R\XPub\Shared\Plugin\PluginContext;
use Psr\Log\LoggerInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;
use function DI\value;

final readonly class InfrastructureContainerConfigurator implements ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Core repositories and services
            PublisherRepositoryInterface::class => autowire(PublisherRepository::class),
            SettingsRepositoryInterface::class => autowire(WordpressSettingsRepository::class),
            QueueRepositoryInterface::class => autowire(WPDBQueueRepository::class),
            \wpdb::class => value($GLOBALS['wpdb']),
            PostStatusRepositoryInterface::class => autowire(WpPostStatusRepository::class),

            // Factories and helpers
            ArticleFactoryInterface::class => autowire(ArticleFactory::class),
            WordpressArticleFactoryInterface::class => get(ArticleFactory::class),
            RendersPostContentInterface::class => autowire(WpPostContentRenderer::class),
            HtmlToMarkdownRendererInterface::class => factory([HtmlToMarkdownRendererFactory::class, 'create']),
            PublisherFactoryInterface::class => autowire(PublisherFactoryService::class),

            // Views & translations
            ViewInterface::class => create(View::class),
            TranslatesMessagesInterface::class => create(Translator::class),

            // Other utilities
            FilterDispatcherInterface::class => autowire(WordpressFilterDispatcher::class),
            HookDispatcherInterface::class => autowire(WordpressHookDispatcher::class),
            SettingsSaveHandler::class => autowire(SettingsSaveHandler::class),
            ReactAppLoader::class => autowire(ReactAppLoader::class),
            XPubSettingsAppLoader::class => autowire(XPubSettingsAppLoader::class),
            SettingsPageRegistrar::class => create()->constructor(
                get(PublisherSettingsService::class),
                get(XPubSettingsAppLoader::class),
            ),
            MetaBox::class => autowire(MetaBox::class),
            LoggerInterface::class => factory(fn() => LoggerFactory::create()),
            OAuthController::class => autowire(OAuthController::class),
            SettingsController::class => autowire(SettingsController::class),
            ClearContainerCacheInterface::class => create(ContainerCacheCleaner::class)
                ->constructor(get(PluginContext::class)),
            PluginUpdateManager::class => create()->constructor(
                get(PluginContext::class),
                autowire(ReleaseService::class),
                get(ClearContainerCacheInterface::class)
            ),
            PublisherSelector::class => create()->constructor(
                get(PublisherRepositoryInterface::class),
                get(PublisherTargetProvider::class),
                get(PublisherFactoryInterface::class),
                get(LoggerInterface::class)
            ),

        ]);
    }
}
