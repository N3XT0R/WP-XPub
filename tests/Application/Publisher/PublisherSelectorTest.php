<?php

namespace N3XT0R\XPub\Tests\Application\Publisher;

use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PublisherSelectorTest extends TestCase
{
    public function testGetReturnsPublisherInstance(): void
    {
        $publisherEntity = new Publisher('test', 'Test');
        $repository = $this->createMock(PublisherRepositoryInterface::class);
        $repository->method('findBySlug')->with('test')->willReturn($publisherEntity);

        $factory = $this->createMock(PublisherFactoryInterface::class);
        $publisherInstance = $this->createMock(PublisherInterface::class);
        $factory->method('createWithConfig')->with('test', [])->willReturn($publisherInstance);

        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return []; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $targetProvider = new PublisherTargetProvider($settings);
        $selector = new PublisherSelector($repository, $targetProvider, $factory, new NullLogger());
        $this->assertSame($publisherInstance, $selector->get('test'));
    }

    public function testGetThrowsForMissingPublisher(): void
    {
        $repository = $this->createMock(PublisherRepositoryInterface::class);
        $repository->method('findBySlug')->willReturn(null);

        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return []; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $targetProvider = new PublisherTargetProvider($settings);
        $selector = new PublisherSelector($repository, $targetProvider, $this->createMock(PublisherFactoryInterface::class));
        $this->expectException(\RuntimeException::class);
        $selector->get('missing');
    }

    public function testGetActiveReturnsInstancesForTargets(): void
    {
        $publisherEntity = new Publisher('active', 'Active');
        $repository = $this->createMock(PublisherRepositoryInterface::class);
        $repository->method('findBySlug')->with('active')->willReturn($publisherEntity);
        $repository->method('all')->willReturn([$publisherEntity]);

        $settings = new class implements \N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return ['active']; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };
        $targetProvider = new PublisherTargetProvider($settings);

        $factory = $this->createMock(PublisherFactoryInterface::class);
        $publisherInstance = $this->createMock(PublisherInterface::class);
        $factory->method('createWithConfig')->willReturn($publisherInstance);

        $selector = new PublisherSelector($repository, $targetProvider, $factory, new NullLogger());
        $result = $selector->getActive();
        $this->assertArrayHasKey('active', $result);
        $this->assertSame($publisherInstance, $result['active']);
    }
}
