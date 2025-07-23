<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder\PublisherSeeder;

final class PublisherSeederHelper
{
    /**
     * Static convenience method to register a publisher.
     */
    public static function register(string $slug, string $name, array $config = []): bool
    {
        $seeder = new PublisherSeeder(new PublisherRepository());

        return $seeder->register($slug, $name, $config);
    }

    public static function upsert(string $slug, string $name, array $config = []): bool
    {
        $seeder = new PublisherSeeder(new PublisherRepository());

        return $seeder->upsert($slug, $name, $config);
    }
}
