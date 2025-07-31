<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\DI;

use DI\ContainerBuilder;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;

class ApplicationContainerConfigurator implements ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Application Services hier registrieren
            \N3XT0R\XPub\Application\Service\SomeApplicationService::class => \DI\autowire(),
        ]);
    }
}