<?php

namespace N3XT0R\XPub\Tests\Domain\Service;

use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use PHPUnit\Framework\TestCase;

class ArticlePublisherTest extends TestCase
{
    public function testPublishCallsPublishers(): void
    {
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects($this->once())
            ->method('publish')
            ->willReturn(true);

        $articlePublisher = new ArticlePublisher([$publisher]);
        $articlePublisher->publish(new Article(1, 0, 'title', 'content'));
    }

    public function testPublishWillTriggerError(): void
    {
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects($this->once())
            ->method('publish')
            ->willReturn(false);
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        $articlePublisher = new ArticlePublisher([$publisher], $logger);
        $articlePublisher->publish(new Article(1, 0, 'title', 'content'));
        self::assertTrue($testHandler->hasErrorRecords());
        self::assertTrue($testHandler->hasRecordThatContains('Publishing failed for post', Level::Error));
    }
}
