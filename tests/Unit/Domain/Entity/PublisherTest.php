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

    public function testCategorizedConfigAndSetConfig(): void
    {
        $configs = [
            new PublisherConfig('clientId', 'id', 'oauth'),
            new PublisherConfig('api_key', 'secret', 'default'),
        ];
        $publisher = new Publisher('slug', 'Name', $configs);

        $expected = [
            'oauth' => ['clientId' => 'id'],
            'default' => ['api_key' => 'secret'],
        ];
        $this->assertSame($expected, $publisher->getCategorizedConfigArray());

        $new = [new PublisherConfig('n', 'v')];
        $publisher->setConfig($new);
        $this->assertSame($new, $publisher->getConfigs());
    }
}
