<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

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
        $postId = $article->post_parent > 0 ? $article->post_parent : $article->postId;
        $scheduledAt = $article->scheduledAt ?? new \DateTimeImmutable();
        $article->scheduledAt = $scheduledAt;

        foreach ($this->publisherSelector->getAll() as $key => $publisher) {
            $job = new Job(
                postId: $postId,
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
