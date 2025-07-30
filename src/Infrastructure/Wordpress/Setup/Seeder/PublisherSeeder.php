<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder;

use InvalidArgumentException;
use N3XT0R\XPub\Domain\Config\PurposeType;
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

    /**
     * Registers an OAuth publisher, required keys depend on the grant_type.
     *
     * @throws InvalidArgumentException if required config keys are missing
     */
    public function registerOAuthWithGrantType(string $slug, string $name, array $config = []): bool
    {
        if ($this->repository->findBySlug($slug)) {
            return false;
        }

        $grantType = $config['grant_type'] ?? 'authorization_code';
        
        $requiredByGrant = match ($grantType) {
            'client_credentials' => ['clientId', 'clientSecret', 'urlAccessToken'],
            'authorization_code' => ['clientId', 'clientSecret', 'redirectUri', 'urlAuthorize', 'urlAccessToken'],
            default => throw new InvalidArgumentException("Unsupported grant_type: $grantType"),
        };

        $missing = array_filter(
            $requiredByGrant,
            static fn(string $key): bool => !array_key_exists($key, $config)
        );

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                "Missing required config keys for grant_type '$grantType': ".implode(', ', $missing)
            );
        }

        $config['grant_type'] = $grantType;

        $configWithPurpose = [];
        foreach ($config as $key => $value) {
            $configWithPurpose[$key] = [
                'value' => $value,
                'purpose_type' => PurposeType::OAUTH,
            ];
        }

        return $this->repository->create($slug, $name, $configWithPurpose);
    }
}
