<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Publisher;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use Psr\Log\LoggerInterface;

final readonly class PublisherSelector
{
    public function __construct(
        private PublisherRepositoryInterface $repository,
        private PublisherTargetProvider $provider,
        private PublisherFactory $factory,
        private ?LoggerInterface $logger = null
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
                $instances[$slug] = $this->get($slug);
            } catch (\Throwable $e) {
                $this->logger?->warning(
                    "Failed to create publisher for slug '{$slug}': ".$e->getMessage(),
                    ['exception' => $e]
                );
            }
        }

        return $instances;
    }

    public function getActive(): array
    {
        $instances = [];
        $targets = $this->provider->getTargets();
        foreach ($targets as $target) {
            try {
                $instances[$target] = $this->get($target);
            } catch (\Throwable $e) {
                $this->logger?->warning(
                    "Failed to create publisher for slug '{$target}': ".$e->getMessage(),
                    ['exception' => $e]
                );
            }
        }

        return $instances;
    }
}
