<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Publisher;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use Psr\Log\LoggerInterface;

final class PublisherSelector
{
    public function __construct(
        private readonly PublisherRepositoryInterface $repository,
        private readonly PublisherFactory $factory,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function get(string $slug): PublisherInterface
    {
        $publisher = $this->repository->findBySlug($slug);

        if (!$publisher) {
            throw new \RuntimeException("Publisher with slug '{$slug}' not found.");
        }

        return $this->factory->createWithConfig($slug, $publisher->getConfigArray());
    }

    /**
     * @return PublisherInterface[]
     */
    public function getAll(): array
    {
        $instances = [];

        foreach ($this->repository->all() as $publisher) {
            $slug = $publisher->getSlug();
            try {
                $instances[$slug] = $this->factory->createWithConfig(
                    $slug,
                    $publisher->getConfigArray()
                );
            } catch (\Throwable $e) {
                $this->logger?->warning(
                    "Failed to create publisher for slug '{$slug}': ".$e->getMessage(),
                    ['exception' => $e]
                );
            }
        }

        return $instances;
    }
}
