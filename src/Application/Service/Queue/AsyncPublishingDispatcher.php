<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Job;

class AsyncPublishingDispatcher
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue
    ) {
    }

    public function dispatch(int $postId, string $publisherKey, array $payload): void
    {
        $job = new Job(
            postId: $postId,
            publisherKey: $publisherKey,
            payload: $payload,
            scheduledAt: new DateTimeImmutable(),
        );

        $this->queue->enqueue($job);
    }
}
