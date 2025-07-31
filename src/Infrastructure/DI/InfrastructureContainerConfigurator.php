<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\DI;

use DI\ContainerBuilder;
use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

use function DI\autowire;
use function DI\create;

final readonly class InfrastructureContainerConfigurator implements ContainerConfiguratorInterface
{
    public function __construct(private PluginContext $pluginContext)
    {
    }

    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            PluginUpdateManager::class => create()->constructor(
                plugin_basename($this->pluginContext->pluginFile),
                $this->pluginContext->pluginSlug,
                $this->pluginContext->pluginInfoUrl,
                autowire(ReleaseService::class)
            ),
            // Core repositories and services
            PublisherRepositoryInterface::class => autowire(PublisherRepository::class),
            SettingsRepositoryInterface::class => autowire(WordpressSettingsRepository::class),
            // Other utilities
            FilterDispatcherInterface::class => autowire(WordpressFilterDispatcher::class),
            HookDispatcherInterface::class => autowire(WordpressHookDispatcher::class),
        ]);
    }
}
