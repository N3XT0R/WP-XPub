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

                // Skip if article is not published
                if (!$this->isPublishedAndNotOutdated($article->postId)) {
                    $this->logger?->info("Skipping job {$job->id} for unpublished post {$article->postId}");
                    continue;
                }


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

    /**
     * Checks whether a given WordPress post is published and has no newer unpublished revision.
     *
     * This ensures that only the latest published version is processed.
     * If a newer revision exists (e.g. a draft or scheduled update), the method returns false.
     *
     * @param  int  $postId  The ID of the parent (published) post.
     *
     * @return bool True if the post is published and not outdated by a newer unpublished revision; false otherwise.
     */
    private function isPublishedAndNotOutdated(int $postId): bool
    {
        $post = get_post($postId);

        if (!$post instanceof \WP_Post || $post->post_status !== 'publish') {
            $this->logger?->info("Post {$postId} is not published");
            return false;
        }

        $revisions = wp_get_post_revisions($postId);

        foreach ($revisions as $rev) {
            // Check if revision is newer and not published (i.e., still being edited)
            if (
                $rev->post_modified_gmt > $post->post_modified_gmt &&
                $rev->post_status !== 'publish'
            ) {
                $this->logger?->info("Post {$postId} has newer unpublished revision {$rev->ID}");
                return false;
            }
        }

        return true;
    }

}
