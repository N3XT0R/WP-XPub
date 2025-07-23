<?php

namespace N3XT0R\XPub\Domain\Service;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;

final class ArticlePublisher
{
    public function __construct(
        private readonly PublisherInterface $publisher
    ) {
    }

    public function publish(Article $article): bool
    {
        return $this->publisher->publish($article);
    }
}
