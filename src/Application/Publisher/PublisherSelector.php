<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Publisher;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;

final class PublisherSelector
{
    public function __construct(
        private readonly SettingsRepositoryInterface $settings,
        private readonly ?LoggerInterface $logger = null
    ) {
    }


    public function get(string $key): PublisherInterface
    {
        return PublisherFactory::create($key);
    }

    /**
     * @return PublisherInterface[]
     */
    public function getAll(): array
    {
        return PublisherFactory::all();
    }

    /**
     * @return PublisherInterface[]
     */
    public function getActive(): array
    {
        $activeSlugs = $this->settings->get('xpub_publisher_targets', []);
        $instances = [];

        foreach ($activeSlugs as $slug) {
            try {
                $instances[$slug] = PublisherFactory::create($slug);
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
