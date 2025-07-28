<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;

class JobRunner
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue,
        private readonly PublisherFactory $publisherFactory
    ) {
    }

    public function run(): void
    {
        $jobs = $this->queue->getDueJobs(new DateTimeImmutable());

        foreach ($jobs as $job) {
            try {
                $publisher = $this->publisherFactory->create($job->publisherKey);
                $publisher->publish($article);
                $this->queue->markAsDone($job);
            } catch (\Throwable $e) {
                $this->queue->markAsFailed($job, $e->getMessage());
            }
        }
    }
}
