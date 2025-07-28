<?php

namespace N3XT0R\XPub\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;

class PublisherTest extends TestCase
{
    public function testConfigArrayRetrieval(): void
    {
        $publisher = new Publisher('devto', 'DevTo', [
            new PublisherConfig('api_key', 'secret')
        ]);

        $this->assertSame('devto', $publisher->getSlug());
        $this->assertSame(['api_key' => 'secret'], $publisher->getConfigArray());
        $this->assertSame('secret', $publisher->getConfigByKey('api_key'));
    }
}
