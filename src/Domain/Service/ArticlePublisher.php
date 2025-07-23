<?php

namespace N3XT0R\XPub\Domain\Service;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;

final class ArticlePublisher
{

    /**
     * @var PublisherInterface[]
     */
    private array $publishers;

    public function __construct(array $publishers)
    {
        $this->publishers = $publishers;
    }

    public function addPublisher(PublisherInterface $publisher): void
    {
        $this->publishers[] = $publisher;
    }

    public function publish(Article $article): void
    {
        foreach ($this->publishers as $publisher) {
            $success = $publisher->publish($article);
            if (!$success) {
                LoggerFactory::create()->error("Publishing failed for post {$article->postId}");
            }
        }
    }
}
