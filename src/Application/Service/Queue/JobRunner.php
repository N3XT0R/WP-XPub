<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use Psr\Log\LoggerInterface;

class JobRunner
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue,
        private readonly PublisherSelector $publisherSelector,
        private readonly ArticleFactoryInterface $articleFactory,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function run(): void
    {
        $jobs = $this->queue->getDueJobs(new DateTimeImmutable());

        foreach ($jobs as $job) {
            try {
                $publisher = $this->publisherSelector->get($job->publisherKey);
                $article = $this->articleFactory->fromArray($job->payload);

                $publisher->publish($article);
                $this->queue->markAsDone($job);
            } catch (\Throwable $e) {
                $this->logger?->warning(
                    sprintf("Failed to publish job %d: %s", $job->id, $e->getMessage()),
                    ['exception' => $e]
                );

                $this->queue->markAsFailed($job, $e->getMessage());
            }
        }
    }
}
