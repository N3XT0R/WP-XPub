<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Job;

final readonly class AsyncPublishingDispatcher
{
    public function __construct(
        private QueueRepositoryInterface $queue,
        private PublisherSelector $publisherSelector,
        private ArticleFactoryInterface $articleFactory,
    ) {
    }

    public function dispatch(Article $article): void
    {
        $scheduledAt = $article->scheduledAt ?? new DateTimeImmutable();
        
        foreach ($this->publisherSelector->getAll() as $key => $publisher) {
            $job = new Job(
                postId: $article->postId,
                publisherKey: $key,
                payload: $this->articleFactory->toArray($article),
                scheduledAt: $scheduledAt,
                attempts: 0,
                lastError: null,
                id: null
            );

            $this->queue->enqueue($job);
        }
    }
}
