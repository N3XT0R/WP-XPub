<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Shared\DI;

use DI\ContainerBuilder;


interface ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void;
}