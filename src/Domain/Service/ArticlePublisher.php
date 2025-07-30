<?php

namespace N3XT0R\XPub\Domain\Service;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ArticlePublisher
{

    /**
     * @var PublisherInterface[]
     */
    private array $publishers;

    private LoggerInterface $logger;

    public function __construct(array $publishers, ?LoggerInterface $logger = null)
    {
        $this->publishers = $publishers;
        $this->logger = $logger ?? new NullLogger();
    }

    public function publish(Article $article): void
    {
        foreach ($this->publishers as $publisher) {
            $success = $publisher->publish($article);
            if (!$success) {
                $this->logger->error("Publishing failed for post {$article->postId}");
            }
        }
    }
}
