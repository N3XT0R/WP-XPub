<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

interface PublisherFactoryInterface
{
    public function create(string $slug): PublisherInterface;

    public function createWithConfig(string $slug, array $config): PublisherInterface;
}

