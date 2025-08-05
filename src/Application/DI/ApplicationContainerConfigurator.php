<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\DI;

use DI\ContainerBuilder;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Domain\Contracts\ReleaseProviderInterface;
use N3XT0R\XPub\Domain\Service\Admin\PublisherSettingsServiceInterface;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;

use function DI\autowire;

class ApplicationContainerConfigurator implements ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            PublisherSettingsServiceInterface::class => autowire(PublisherSettingsService::class),
            PublisherSelector::class => autowire(PublisherSelector::class),
            ReleaseProviderInterface::class => autowire(ReleaseService::class),
        ]);
    }
}