<?php

namespace N3XT0R\XPub\Tests\Application\Factory;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use PHPUnit\Framework\TestCase;

class PublisherFactoryTest extends TestCase
{
    public function testCreateReturnsPublisher(): void
    {
        $publisher = PublisherFactory::createWithConfig('devto', ['api_key' => 'k']);
        $this->assertInstanceOf(DevToPublisher::class, $publisher);
    }

    public function testUnknownPublisherThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        PublisherFactory::create('unknown');
    }
}
