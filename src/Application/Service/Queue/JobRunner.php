<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Publishers\Contracts\SupportsOAuthFactoryInterface;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use Psr\Log\LoggerInterface;

class JobRunner
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue,
        private readonly PublisherSelector $publisherSelector,
        private readonly ArticleFactoryInterface $articleFactory,
        private readonly PostStatusRepositoryInterface $postStatusRepository,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function run(): void
    {
        $jobs = $this->queue->getAllDueJobs(new DateTimeImmutable());

        foreach ($jobs as $job) {
            try {
                $publisher = $this->publisherSelector->get($job->publisherKey);
                $article = $this->articleFactory->fromArray($job->payload);

                // Skip if article is not published
                if (!$this->postStatusRepository->isPublishedAndNotOutdated($article->postId)) {
                    $this->logger?->info("Skipping job {$job->id} for unpublished post {$article->postId}");
                    continue;
                }


                $publisher->publish($article);
                $this->queue->markAsDone($job);
            } catch (\Throwable $e) {
                $this->logger?->error(
                    sprintf("Failed to publish job %d: %s", $job->id, $e->getMessage()),
                    ['exception' => $e]
                );

                $this->queue->markAsFailed($job, $e->getMessage());
            }
        }
    }

    public function refreshTokens(): void
    {
        $publishers = $this->publisherSelector->getActive();

        foreach ($publishers as $slug => $publisher) {
            if (!$publisher instanceof SupportsOAuthFactoryInterface) {
                error_log('no support oauth interface'.$slug);
                continue;
            }

            $factory = $publisher->getOAuthTokenProviderFactory();
            if (!$factory) {
                error_log('no factory'.$slug);
                continue;
            }

            try {
                $provider = $factory->createFromPublisherSlug($slug);
                if ($provider->hasRefreshToken()) {
                    if ($provider->shouldRefreshToken() && $provider->refreshToken()) {
                        $this->logger?->info("Token refreshed for $slug");
                    } else {
                        $this->logger?->info("No need to refresh token for $slug");
                    }
                }
            } catch (\Throwable $e) {
                $this->logger?->error(
                    sprintf("Failed to refresh token for %s: %s", $slug, $e->getMessage()),
                    ['exception' => $e]
                );
            }
        }
    }
}
