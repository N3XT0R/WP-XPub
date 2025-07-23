<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

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
     */
    public function register(string $slug, string $name, array $config = []): bool
    {
        // Prüfen ob der Publisher bereits existiert
        if ($this->repository->findBySlug($slug)) {
            return false;
        }

        // Pflichtfelder sicherstellen
        $requiredKeys = ['api_key'];
        $missing = array_diff($requiredKeys, array_keys($config));
        if (!empty($missing)) {
            throw new InvalidArgumentException('Missing required config keys: '.implode(', ', $missing));
        }

        // Publisher + Konfiguration anlegen
        return $this->repository->create($slug, $name, $config);
    }

    /**
     * Force update or insert of publisher + config.
     */
    public function upsert(string $slug, string $name, array $config = []): bool
    {
        $existing = $this->repository->findBySlug($slug);

        if ($existing === null) {
            return $this->register($slug, $name, $config);
        }

        foreach ($config as $key => $value) {
            $this->repository->updateConfig($slug, [$key => $value]);
        }

        return true;
    }
}
