<?php

declare(strict_types=1);

use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\Factory\WordpressArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\HtmlToMarkdownRendererInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Contracts\ReleaseProviderInterface;
use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Domain\Contracts\TranslatesMessagesInterface;
use N3XT0R\XPub\Domain\Contracts\ViewInterface;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRendererFactory;
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactoryService;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WPDBQueueRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WpPostStatusRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use Psr\Log\LoggerInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

return [
    // Core repositories and services
    QueueRepositoryInterface::class => factory(fn() => new WPDBQueueRepository(Database::get())),
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
    ReleaseProviderInterface::class => autowire(ReleaseService::class),
    SettingsSaveHandler::class => autowire(SettingsSaveHandler::class),
    SettingsPageRegistrar::class => autowire(SettingsPageRegistrar::class),
    MetaBox::class => autowire(MetaBox::class),
    OAuthTokenProviderFactory::class => autowire(OAuthTokenProviderFactory::class),
    OAuthController::class => autowire(OAuthController::class),
    PluginUpdateManager::class => autowire(PluginUpdateManager::class),
    LoggerInterface::class => factory([LoggerFactory::class, 'create']),
];
