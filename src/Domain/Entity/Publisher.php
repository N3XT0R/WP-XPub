<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Entity;

final class Publisher
{
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

    public function getConfigArray(): array
    {
        $out = [];
        foreach ($this->configs as $c) {
            $out[$c->getKey()] = $c->getValue();
        }
        return $out;
    }
}