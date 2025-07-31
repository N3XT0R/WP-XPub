<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Service\Queue;

use DateTimeImmutable;
use N3XT0R\XPub\Application\Factory\ArticleFactory;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Job;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Tests\Stubs\InMemoryQueue;
use N3XT0R\XPub\Tests\Stubs\InMemorySettingsRepository;
use N3XT0R\XPub\Tests\Stubs\SimplePublisher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class JobRunnerTest extends TestCase
{
    private function createSelector(SimplePublisher $publisher): PublisherSelector
    {
        $entity = new Publisher('simple', 'Simple');
        $repo = new class($entity) implements PublisherRepositoryInterface {
            public function __construct(private Publisher $p) {}
            public function all(): array { return [$this->p]; }
            public function findBySlug(string $slug, ?string $purposeType = null): ?Publisher { return $this->p; }
            public function updateConfig(string $slug, array $newConfig): bool { return true; }
            public function create(string $slug, string $name, array $config): bool { return true; }
        };
        $settings = new InMemorySettingsRepository([
            'xpub_publisher_targets' => ['simple'],
        ]);
        $provider = new PublisherTargetProvider($settings);
        $factory = new class($publisher) implements PublisherFactoryInterface {
            public function __construct(private PublisherInterface $p) {}
            public function create(string $slug): PublisherInterface { return $this->p; }
            public function createWithConfig(string $slug, array $config): PublisherInterface { return $this->p; }
        };
        return new PublisherSelector($repo, $provider, $factory, new NullLogger());
    }

    private function createRunner(SimplePublisher $publisher, InMemoryQueue $queue, bool $published): JobRunner
    {
        $selector = $this->createSelector($publisher);
        $factory = new ArticleFactory();
        $statusRepo = new class($published) implements PostStatusRepositoryInterface {
            public function __construct(private bool $ok) {}
            public function isPublishedAndNotOutdated(int $postId): bool { return $this->ok; }
        };
        return new JobRunner($queue, $selector, $factory, $statusRepo, new NullLogger());
    }

    public function testRunPublishesAndMarksDone(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $runner = $this->createRunner($publisher, $queue, true);

        $factory = new ArticleFactory();
        $article = new Article(1, 0, 'Title', 'Content', scheduledAt: new DateTimeImmutable('2024-01-01'));
        $payload = $factory->toArray($article);
        $queue->jobs[] = new Job(1, 'simple', $payload, $article->scheduledAt, 0, null, 5);

        $runner->run();

        $this->assertCount(1, $publisher->published);
        $this->assertCount(1, $queue->done);
        $this->assertEmpty($queue->failed);
    }

    public function testRunSkipsWhenUnpublished(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $runner = $this->createRunner($publisher, $queue, false);

        $factory = new ArticleFactory();
        $article = new Article(2, 0, 'T', 'C', scheduledAt: new DateTimeImmutable('2024-01-01'));
        $queue->jobs[] = new Job(2, 'simple', $factory->toArray($article), $article->scheduledAt);

        $runner->run();

        $this->assertEmpty($publisher->published);
        $this->assertEmpty($queue->done);
        $this->assertEmpty($queue->failed);
    }

    public function testRunMarksFailedOnException(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $publisher->shouldFail = true;
        $runner = $this->createRunner($publisher, $queue, true);

        $factory = new ArticleFactory();
        $article = new Article(3, 0, 'T', 'C', scheduledAt: new DateTimeImmutable('2024-01-01'));
        $queue->jobs[] = new Job(3, 'simple', $factory->toArray($article), $article->scheduledAt);

        $runner->run();

        $this->assertCount(1, $queue->failed);
        $this->assertEmpty($queue->done);
    }
}
