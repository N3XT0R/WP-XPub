<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Job;

final class AsyncPublishingDispatcher
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue,
        private readonly PublisherSelector $publisherSelector,
        private readonly ArticleFactoryInterface $articleFactory,
    ) {
    }

    public function dispatch(Article $article): void
    {
        $now = new DateTimeImmutable();

        foreach ($this->publisherSelector->getAll() as $key => $publisher) {
            $job = new Job(
                postId: $article->postId,
                publisherKey: $key,
                payload: $this->articleFactory->toArray($article),
                scheduledAt: $now
            );

            $this->queue->enqueue($job);
        }
    }
}
