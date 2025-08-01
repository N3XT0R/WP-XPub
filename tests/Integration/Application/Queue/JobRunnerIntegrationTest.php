<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Integration\Application\Queue;

use N3XT0R\XPub\Application\Factory\ArticleFactory;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\AsyncPublishingDispatcher;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Tests\Stubs\InMemoryQueue;
use N3XT0R\XPub\Tests\Stubs\SimplePublisher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class JobRunnerIntegrationTest extends TestCase
{
    private function createSelector(SimplePublisher $publisher): PublisherSelector
    {
        $entity = new Publisher('simple', 'Simple');
        $repo = new class($entity) implements PublisherRepositoryInterface {
            public function __construct(private Publisher $p)
            {
            }

            public function all(): array
            {
                return [$this->p];
            }

            public function findBySlug(string $slug, ?string $purposeType = null): ?Publisher
            {
                return $this->p;
            }

            public function updateConfig(string $slug, array $newConfig): bool
            {
                return true;
            }

            public function create(string $slug, string $name, array $config): bool
            {
                return true;
            }

            public function delete(string $key): bool
            {
                return true;
            }
        };
        $settings = new class implements SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed
            {
                return ['simple'];
            }

            public function set(string $key, mixed $value): bool
            {
                return true;
            }

            public function delete(string $key): bool
            {
                return true;
            }
        };
        $provider = new PublisherTargetProvider($settings);
        $factory = new class($publisher) implements PublisherFactoryInterface {
            public function __construct(private PublisherInterface $p)
            {
            }

            public function create(string $slug): PublisherInterface
            {
                return $this->p;
            }

            public function createWithConfig(string $slug, array $config): PublisherInterface
            {
                return $this->p;
            }
        };
        return new PublisherSelector($repo, $provider, $factory, new NullLogger());
    }

    private function createRunner(SimplePublisher $publisher, InMemoryQueue $queue, bool $published): JobRunner
    {
        $selector = $this->createSelector($publisher);
        $factory = new ArticleFactory();
        $statusRepo = new class($published) implements PostStatusRepositoryInterface {
            public function __construct(private bool $ok)
            {
            }

            public function isPublishedAndNotOutdated(int $postId): bool
            {
                return $this->ok;
            }
        };
        return new JobRunner($queue, $selector, $factory, $statusRepo, new NullLogger());
    }

    private function dispatchArticle(AsyncPublishingDispatcher $dispatcher): Article
    {
        $article = new Article(1, 0, 'T', 'C', scheduledAt: new \DateTimeImmutable('2023-01-01'));
        $dispatcher->dispatch($article);
        return $article;
    }

    public function testRunPublishesAndMarksDone(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $selector = $this->createSelector($publisher);
        $dispatcher = new AsyncPublishingDispatcher($queue, $selector, new ArticleFactory());
        $this->dispatchArticle($dispatcher);

        $runner = $this->createRunner($publisher, $queue, true);
        $runner->run();

        $this->assertCount(1, $publisher->published);
        $this->assertCount(1, $queue->done);
        $this->assertEmpty($queue->failed);
    }

    public function testRunSkipsUnpublishedArticle(): void
    {
        $queue = new InMemoryQueue();
        $publisher = new SimplePublisher();
        $selector = $this->createSelector($publisher);
        $dispatcher = new AsyncPublishingDispatcher($queue, $selector, new ArticleFactory());
        $this->dispatchArticle($dispatcher);

        $runner = $this->createRunner($publisher, $queue, false);
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
        $selector = $this->createSelector($publisher);
        $dispatcher = new AsyncPublishingDispatcher($queue, $selector, new ArticleFactory());
        $this->dispatchArticle($dispatcher);

        $runner = $this->createRunner($publisher, $queue, true);
        $runner->run();

        $this->assertCount(1, $queue->failed);
        $this->assertEmpty($queue->done);
    }
}
