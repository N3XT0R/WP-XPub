<?php

declare(strict_types=1);


namespace N3XT0R\XPub\Domain\Publisher\Contracts;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

interface PublisherFactoryInterface
{

    public static function create(string $target): PublisherInterface;

    public static function createWithConfig(string $target, array $config): PublisherInterface;

    public static function all(): array;
}