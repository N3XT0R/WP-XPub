<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Service\Queue;

use N3XT0R\XPub\Application\Factory\ArticleFactory;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\AsyncPublishingDispatcher;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Tests\Stubs\InMemoryQueue;
use N3XT0R\XPub\Tests\Stubs\InMemorySettingsRepository;
use N3XT0R\XPub\Tests\Stubs\SimplePublisher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class AsyncPublishingDispatcherTest extends TestCase
{
    private function createSelector(SimplePublisher $publisher): PublisherSelector
    {
        $publisherEntity = new Publisher('simple', 'Simple');

        $repo = new class($publisherEntity) implements PublisherRepositoryInterface {
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

    public function testDispatchCreatesJobsForPublishers(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $selector = $this->createSelector($publisher);
        $factory = new ArticleFactory();
        $dispatcher = new AsyncPublishingDispatcher($queue, $selector, $factory);

        $article = new Article(1, 0, 'Title', 'Content', scheduledAt: new \DateTimeImmutable('2023-01-01 00:00:00'));
        $dispatcher->dispatch($article);

        $this->assertCount(1, $queue->jobs);
        $job = $queue->jobs[0];
        $this->assertSame('simple', $job->publisherKey);
        $this->assertSame($factory->toArray($article), $job->payload);
        $this->assertEquals($article->scheduledAt, $job->scheduledAt);
    }

    public function testDispatchAddsScheduledAtWhenMissing(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $selector = $this->createSelector($publisher);
        $factory = new ArticleFactory();
        $dispatcher = new AsyncPublishingDispatcher($queue, $selector, $factory);

        $article = new Article(2, 0, 'Title2', 'Content2');
        $this->assertNull($article->scheduledAt);
        $dispatcher->dispatch($article);

        $this->assertNotNull($article->scheduledAt);
        $this->assertEquals($article->scheduledAt, $queue->jobs[0]->scheduledAt);
    }
}
