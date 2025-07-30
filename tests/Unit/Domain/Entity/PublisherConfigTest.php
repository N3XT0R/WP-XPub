<?php

namespace N3XT0R\XPub\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;

class PublisherConfigTest extends TestCase
{
    public function testGetters(): void
    {
        $config = new PublisherConfig('api_key', 'secret');
        $this->assertSame('api_key', $config->getKey());
        $this->assertSame('secret', $config->getValue());
    }

    public function testPurposeTypeAndAsArray(): void
    {
        $config = new PublisherConfig('k', 'v', 'custom');
        $this->assertSame('custom', $config->getPurposeType());
        $this->assertSame([
            'key' => 'k',
            'value' => 'v',
            'purpose_type' => 'custom',
        ], $config->asArray());
    }
}
