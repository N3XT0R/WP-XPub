<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Repository;

use N3XT0R\XPub\Domain\Entity\Publisher;

interface PublisherRepositoryInterface
{
    /**
     * @return Publisher[]
     */
    public function all(): array;
}