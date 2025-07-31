<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\DI;

use DI\ContainerBuilder;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;

class DomainContainerConfigurator implements ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Domain Services, Entities oder Policies
            \N3XT0R\XPub\Domain\Model\MyService::class => \DI\autowire(),
        ]);
    }
}