<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Integration\Domain\Entity;

use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;
use PHPUnit\Framework\TestCase;

final class PublisherIntegrationTest extends TestCase
{
    public function testGetConfigByKeyReturnsNullWhenMissing(): void
    {
        $publisher = new Publisher('slug', 'Name', [new PublisherConfig('k', 'v')]);
        $this->assertNull($publisher->getConfigByKey('missing'));
    }
}
