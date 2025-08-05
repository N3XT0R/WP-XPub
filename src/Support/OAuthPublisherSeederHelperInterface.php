<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

interface OAuthPublisherSeederHelperInterface
{
    public static function register(string $slug, string $name, array $config = []): bool;

    public static function upsert(string $slug, string $name, array $config = []): bool;

    public static function unregister(string $slug): bool;
}
