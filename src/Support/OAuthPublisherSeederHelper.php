<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

use N3XT0R\XPub\Domain\Config\oAuth\OAuthGrantValidator;
use N3XT0R\XPub\Domain\Config\oAuth\OAuthPurposeTransformer;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder\PublisherSeeder;

final class OAuthPublisherSeederHelper implements OAuthPublisherSeederHelperInterface
{
    private static function makeSeeder(): PublisherSeeder
    {
        return new PublisherSeeder(
            new PublisherRepository(),
            [
                new OAuthGrantValidator(),
            ],
            [
                new OAuthPurposeTransformer(),
            ]
        );
    }

    public static function register(string $slug, string $name, array $config = []): bool
    {
        return self::makeSeeder()->register($slug, $name, $config);
    }

    public static function upsert(string $slug, string $name, array $config = []): bool
    {
        return self::makeSeeder()->upsert($slug, $name, $config);
    }

    public static function unregister(string $slug): bool
    {
        return self::makeSeeder()->unregister($slug);
    }
}
