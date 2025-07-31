<?php

namespace N3XT0R\XPub\Tests\Application\Publisher;

use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Domain\Contracts\PublisherFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Tests\Stubs\InMemorySettingsRepository;
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

        $settings = new InMemorySettingsRepository([
            'xpub_publisher_targets' => [],
        ]);
        $targetProvider = new PublisherTargetProvider($settings);
        $selector = new PublisherSelector($repository, $targetProvider, $factory, new NullLogger());
        $this->assertSame($publisherInstance, $selector->get('test'));
    }

    public function testGetThrowsForMissingPublisher(): void
    {
        $repository = $this->createMock(PublisherRepositoryInterface::class);
        $repository->method('findBySlug')->willReturn(null);

        $settings = new InMemorySettingsRepository([
            'xpub_publisher_targets' => [],
        ]);
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

        $settings = new InMemorySettingsRepository([
            'xpub_publisher_targets' => ['active'],
        ]);
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
