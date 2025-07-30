<?php

declare(strict_types=1);

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\Factory\WordpressArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\HtmlToMarkdownRendererInterface;
use N3XT0R\XPub\Domain\Contracts\ReleaseProviderInterface;
use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Domain\Contracts\TranslatesMessagesInterface;
use N3XT0R\XPub\Domain\Contracts\ViewInterface;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRendererFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WPDBQueueRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator;
use Psr\Log\LoggerInterface;

return [
    // Core repositories and services
    SettingsRepositoryInterface::class => autowire(WordpressSettingsRepository::class),
    PublisherRepositoryInterface::class => autowire(PublisherRepository::class),
    QueueRepositoryInterface::class => factory(fn () => new WPDBQueueRepository(Database::get())),

    // Factories and helpers
    ArticleFactoryInterface::class => autowire(ArticleFactory::class),
    WordpressArticleFactoryInterface::class => get(ArticleFactory::class),
    RendersPostContentInterface::class => autowire(WpPostContentRenderer::class),
    HtmlToMarkdownRendererInterface::class => factory([HtmlToMarkdownRendererFactory::class, 'create']),

    // Views & translations
    ViewInterface::class => create(View::class),
    TranslatesMessagesInterface::class => create(Translator::class),

    // Other utilities
    ReleaseProviderInterface::class => autowire(ReleaseService::class),
    FilterDispatcherInterface::class => create(WordpressFilterDispatcher::class),
    HookDispatcherInterface::class => create(WordpressHookDispatcher::class),
    SettingsSaveHandler::class => autowire(SettingsSaveHandler::class),
    SettingsPageRegistrar::class => autowire(SettingsPageRegistrar::class),
    MetaBox::class => autowire(MetaBox::class),
    OAuthTokenProviderFactory::class => autowire(OAuthTokenProviderFactory::class),
    OAuthController::class => autowire(OAuthController::class),
    PluginUpdateManager::class => autowire(PluginUpdateManager::class),
    LoggerInterface::class => factory([LoggerFactory::class, 'create']),
];
