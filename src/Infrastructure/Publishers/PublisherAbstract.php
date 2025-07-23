<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

abstract class PublisherAbstract implements PublisherInterface
{
    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }


    public function getByKey(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

}