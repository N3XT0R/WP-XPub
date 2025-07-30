<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

final class PublisherFactoryService implements PublisherFactoryInterface
{
    public function create(string $slug): PublisherInterface
    {
        return PublisherFactory::create($slug);
    }

    public function createWithConfig(string $slug, array $config): PublisherInterface
    {
        return PublisherFactory::createWithConfig($slug, $config);
    }
}

