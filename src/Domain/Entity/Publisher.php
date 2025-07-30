<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Entity;

final class Publisher
{
    /**
     * @param  PublisherConfig[]  $configs
     */
    public function __construct(
        private string $slug,
        private string $name,
        private array $configs = []
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PublisherConfig[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function setConfig(array $configs): void
    {
        $this->configs = $configs;
    }

    public function getConfigArray(): array
    {
        $out = [];
        foreach ($this->configs as $c) {
            $out[$c->getKey()] = $c->getValue();
        }
        return $out;
    }

    public function getCategorizedConfigArray(): array
    {
        $categorized = [];

        foreach ($this->configs as $config) {
            $purposeType = $config->getPurposeType();
            $key = $config->getKey();
            $value = $config->getValue();

            if (!isset($categorized[$purposeType])) {
                $categorized[$purposeType] = [];
            }

            $categorized[$purposeType][$key] = $value;
        }

        return $categorized;
    }

    public function getConfigByKey(string $key): ?string
    {
        foreach ($this->configs as $config) {
            if ($config->getKey() === $key) {
                return $config->getValue();
            }
        }

        return null;
    }
}
