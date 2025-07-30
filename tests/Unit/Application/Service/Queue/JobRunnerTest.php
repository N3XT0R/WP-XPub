<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Service\Queue;

use DateTimeImmutable;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Entity\Job;
use N3XT0R\XPub\Domain\Repository\PostStatusRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class JobRunnerTest extends TestCase
{
    public function testRunsJobSuccessfully(): void
    {
        $job = new Job(1, 'devto', []);

        $queue = new class([$job]) implements QueueRepositoryInterface {
            public array $done = [];
            public array $failed = [];
            public function __construct(private array $jobs) {}
            public function getAllDueJobs(DateTimeImmutable $now): array { return $this->jobs; }
            public function markAsDone(Job $job): void { $this->done[] = $job; }
            public function markAsFailed(Job $job, string $errorMessage): void { $this->failed[] = [$job, $errorMessage]; }
        };

        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects($this->once())->method('publish')->willReturn(true);

        $publisherEntity = new \N3XT0R\XPub\Domain\Entity\Publisher('devto', 'DevTo');
        $repository = new class($publisherEntity) implements \N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface {
            public function __construct(private $pub) {}
            public function all(): array { return [$this->pub]; }
            public function findBySlug(string $slug, ?string $purposeType = null): ?\N3XT0R\XPub\Domain\Entity\Publisher { return $this->pub; }
            public function updateConfig(string $slug, array $newConfig): bool { return true; }
            public function create(string $slug, string $name, array $config): bool { return true; }
        };
        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return []; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $provider = new \N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider($settings);
        $pubFactory = new class($publisher) implements \N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface {
            public function __construct(private $inst) {}
            public function create(string $slug): PublisherInterface { return $this->inst; }
            public function createWithConfig(string $slug, array $config): PublisherInterface { return $this->inst; }
        };

        $selector = new PublisherSelector($repository, $provider, $pubFactory);

        $article = new Article(1, 0, 't', 'c');
        $factory = $this->createMock(ArticleFactoryInterface::class);
        $factory->method('fromArray')->willReturn($article);

        $statusRepo = $this->createMock(PostStatusRepositoryInterface::class);
        $statusRepo->method('isPublishedAndNotOutdated')->with(1)->willReturn(true);

        $runner = new JobRunner($queue, $selector, $factory, $statusRepo);
        $runner->run();

        $this->assertSame([$job], $queue->done);
        $this->assertEmpty($queue->failed);
    }

    public function testSkipsJobWhenPostNotPublished(): void
    {
        $job = new Job(2, 'devto', []);
        $handler = new TestHandler();
        $logger = new Logger('test', [$handler]);

        $queue = new class([$job]) implements QueueRepositoryInterface {
            public array $done = [];
            public array $failed = [];
            public function __construct(private array $jobs) {}
            public function getAllDueJobs(DateTimeImmutable $now): array { return $this->jobs; }
            public function markAsDone(Job $job): void { $this->done[] = $job; }
            public function markAsFailed(Job $job, string $errorMessage): void { $this->failed[] = [$job, $errorMessage]; }
        };

        $publisher = $this->createMock(PublisherInterface::class);
        $publisherEntity = new \N3XT0R\XPub\Domain\Entity\Publisher('devto', 'DevTo');
        $repository = new class($publisherEntity) implements \N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface {
            public function __construct(private $pub) {}
            public function all(): array { return [$this->pub]; }
            public function findBySlug(string $slug, ?string $purposeType = null): ?\N3XT0R\XPub\Domain\Entity\Publisher { return $this->pub; }
            public function updateConfig(string $slug, array $newConfig): bool { return true; }
            public function create(string $slug, string $name, array $config): bool { return true; }
        };
        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return []; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $provider = new \N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider($settings);
        $pubFactory = new class($publisher) implements \N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface {
            public function __construct(private $inst) {}
            public function create(string $slug): PublisherInterface { return $this->inst; }
            public function createWithConfig(string $slug, array $config): PublisherInterface { return $this->inst; }
        };

        $selector = new PublisherSelector($repository, $provider, $pubFactory);

        $article = new Article(2, 0, 't', 'c');
        $factory = $this->createMock(ArticleFactoryInterface::class);
        $factory->method('fromArray')->willReturn($article);

        $statusRepo = $this->createMock(PostStatusRepositoryInterface::class);
        $statusRepo->method('isPublishedAndNotOutdated')->with(2)->willReturn(false);

        $runner = new JobRunner($queue, $selector, $factory, $statusRepo, $logger);
        $runner->run();

        $this->assertEmpty($queue->done);
        $this->assertEmpty($queue->failed);
        $this->assertTrue($handler->hasInfoThatContains('Skipping job'));
    }

    public function testMarksFailedOnException(): void
    {
        $job = new Job(3, 'devto', []);
        $handler = new TestHandler();
        $logger = new Logger('test', [$handler]);

        $queue = new class([$job]) implements QueueRepositoryInterface {
            public array $done = [];
            public array $failed = [];
            public function __construct(private array $jobs) {}
            public function getAllDueJobs(DateTimeImmutable $now): array { return $this->jobs; }
            public function markAsDone(Job $job): void { $this->done[] = $job; }
            public function markAsFailed(Job $job, string $errorMessage): void { $this->failed[] = [$job, $errorMessage]; }
        };

        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->method('publish')->willThrowException(new \RuntimeException('fail'));

        $publisherEntity = new \N3XT0R\XPub\Domain\Entity\Publisher('devto', 'DevTo');
        $repository = new class($publisherEntity) implements \N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface {
            public function __construct(private $pub) {}
            public function all(): array { return [$this->pub]; }
            public function findBySlug(string $slug, ?string $purposeType = null): ?\N3XT0R\XPub\Domain\Entity\Publisher { return $this->pub; }
            public function updateConfig(string $slug, array $newConfig): bool { return true; }
            public function create(string $slug, string $name, array $config): bool { return true; }
        };
        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return []; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $provider = new \N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider($settings);
        $pubFactory = new class($publisher) implements \N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface {
            public function __construct(private $inst) {}
            public function create(string $slug): PublisherInterface { return $this->inst; }
            public function createWithConfig(string $slug, array $config): PublisherInterface { return $this->inst; }
        };

        $selector = new PublisherSelector($repository, $provider, $pubFactory);

        $article = new Article(3, 0, 't', 'c');
        $factory = $this->createMock(ArticleFactoryInterface::class);
        $factory->method('fromArray')->willReturn($article);

        $statusRepo = $this->createMock(PostStatusRepositoryInterface::class);
        $statusRepo->method('isPublishedAndNotOutdated')->with(3)->willReturn(true);

        $runner = new JobRunner($queue, $selector, $factory, $statusRepo, $logger);
        $runner->run();

        $this->assertEmpty($queue->done);
        $this->assertCount(1, $queue->failed);
        $this->assertTrue($handler->hasErrorThatContains('Failed to publish job'));
    }
}
