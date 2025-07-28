<?php

namespace N3XT0R\XPub\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;

class ArticlePublisherTest extends TestCase
{
    public function testPublishCallsPublishers(): void
    {
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects($this->once())
            ->method('publish')
            ->willReturn(true);

        $articlePublisher = new ArticlePublisher([$publisher]);
        $articlePublisher->publish(new Article(1, 'title', 'content'));
    }
}
