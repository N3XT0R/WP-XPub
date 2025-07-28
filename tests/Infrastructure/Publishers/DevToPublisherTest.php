<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use PHPUnit\Framework\TestCase;

class DevToPublisherTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['wp_remote_post_response']);
    }

    public function testPublishFailsWithoutApiKey(): void
    {
        $publisher = new DevToPublisher();
        $publisher->setConfig(['api_key' => '']);
        $result = $publisher->publish(new Article(1, 't', 'c'));
        $this->assertFalse($result);
    }

    public function testPublishSucceedsWith201Response(): void
    {
        $GLOBALS['wp_remote_post_response'] = ['response' => ['code' => 201], 'body' => ''];
        $publisher = new DevToPublisher();
        $publisher->setConfig(['api_key' => 'abc']);
        $result = $publisher->publish(new Article(2, 't', 'c'));
        $this->assertTrue($result);
    }

    public function testPublishFailsWithUnexpectedResponse(): void
    {
        $GLOBALS['wp_remote_post_response'] = ['response' => ['code' => 400], 'body' => 'bad'];
        $publisher = new DevToPublisher();
        $publisher->setConfig(['api_key' => 'abc']);
        $result = $publisher->publish(new Article(3, 't', 'c'));
        $this->assertFalse($result);
    }
}
