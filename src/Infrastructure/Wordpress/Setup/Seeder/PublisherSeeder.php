<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder;

use InvalidArgumentException;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;

final readonly class PublisherSeeder
{
    public function __construct(
        private PublisherRepositoryInterface $repository
    ) {
    }

    /**
     * Registers a publisher and its config if it does not exist.
     *
     * @throws InvalidArgumentException if required config keys are missing
     */
    public function register(string $slug, string $name, array $config = []): bool
    {
        if ($this->repository->findBySlug($slug)) {
            return false; // Already registered
        }

        // Ensure all required config keys are present (values can be empty)
        $requiredKeys = ['api_key'];
        $missingKeys = array_filter(
            $requiredKeys,
            static fn(string $key): bool => !array_key_exists($key, $config)
        );

        if (!empty($missingKeys)) {
            throw new InvalidArgumentException(
                'Missing required config keys: '.implode(', ', $missingKeys)
            );
        }

        return $this->repository->create($slug, $name, $config);
    }

    /**
     * Inserts or updates publisher and config.
     */
    public function upsert(string $slug, string $name, array $config = []): bool
    {
        if (!$this->repository->findBySlug($slug)) {
            return $this->register($slug, $name, $config);
        }

        // Update existing config values (can be empty strings)
        foreach ($config as $key => $value) {
            $this->repository->updateConfig($slug, [$key => $value]);
        }

        return true;
    }
}
