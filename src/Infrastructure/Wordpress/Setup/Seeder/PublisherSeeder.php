<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder;

use N3XT0R\XPub\Domain\Config\ConfigTransformerInterface;
use N3XT0R\XPub\Domain\Config\ConfigValidatorInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;

final readonly class PublisherSeeder
{
    /**
     * @param  ConfigValidatorInterface[]  $validators
     * @param  ConfigTransformerInterface[]  $transformers
     */
    public function __construct(
        private PublisherRepositoryInterface $repository,
        private iterable $validators = [],
        private iterable $transformers = []
    ) {
    }

    public function register(string $slug, string $name, array $config = []): bool
    {
        if ($this->repository->findBySlug($slug)) {
            return false;
        }

        $this->validateConfig($config);
        $transformed = $this->transformConfig($config);

        return $this->repository->create($slug, $name, $transformed);
    }

    public function unregister(string $slug): bool
    {
        return $this->repository->delete($slug);
    }

    public function upsert(string $slug, string $name, array $config = []): bool
    {
        $this->validateConfig($config);
        $transformed = $this->transformConfig($config);

        if (!$this->repository->findBySlug($slug)) {
            return $this->repository->create($slug, $name, $transformed);
        }

        return $this->repository->updateConfig($slug, $transformed);
    }

    private function validateConfig(array $config): void
    {
        foreach ($this->validators as $validator) {
            if ($validator->supports($config)) {
                $validator->validate($config);
            }
        }
    }

    private function transformConfig(array $config): array
    {
        foreach ($this->transformers as $transformer) {
            return $transformer->transform($config);
        }

        return $config;
    }
}
