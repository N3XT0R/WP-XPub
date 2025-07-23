<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Entity;

final class Publisher
{
    public function __construct(
        private string $slug,
        private string $name,
        private array $config
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getName(): string
    {
        return $this->name;
    }
}