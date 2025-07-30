<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Setup;

use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\Seeder\PublisherSeeder;
use PHPUnit\Framework\TestCase;

class PublisherSeederTest extends TestCase
{

    public function testUpsertUpdatesExistingConfig(): void
    {
        $updated = [];
        $repo = new class($updated) implements PublisherRepositoryInterface {
            private array $updated;

            public function __construct(&$u)
            {
                $this->updated =& $u;
            }

            public function all(): array
            {
                return [];
            }

            public function findBySlug(string $slug, ?string $purposeType = null): ?Publisher
            {
                return new Publisher($slug, 'name', [new PublisherConfig('api_key', '')]);
            }

            public function updateConfig(string $slug, array $newConfig): bool
            {
                $this->updated[$slug] = $newConfig;
                return true;
            }

            public function create(string $slug, string $name, array $config): bool
            {
                return true;
            }
        };

        $seeder = new PublisherSeeder($repo);
        $seeder->upsert('slug', 'name', ['api_key' => 'abc']);
        $this->assertSame(['api_key' => 'abc'], $updated['slug']);
    }
}
