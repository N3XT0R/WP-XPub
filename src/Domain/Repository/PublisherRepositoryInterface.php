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

    public function findBySlug(string $slug): ?Publisher;

    public function updateConfig(string $slug, array $newConfig): void;

}