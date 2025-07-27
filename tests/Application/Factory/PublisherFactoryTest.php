<?php

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;

class PublisherFactoryTest extends TestCase
{
    public function testCreateReturnsPublisher(): void
    {
        $publisher = PublisherFactory::createWithConfig('devto', ['api_key' => 'k']);
        $this->assertInstanceOf(DevToPublisher::class, $publisher);
    }

    public function testUnknownPublisherThrows(): void
    {
        $this->expectException(RuntimeException::class);
        PublisherFactory::create('unknown');
    }
}
