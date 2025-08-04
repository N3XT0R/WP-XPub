<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Service\Admin;

use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class PublisherSettingsServiceTest extends TestCase
{
    public function testGetSettingsViewDataReturnsCorrectStructure(): void
    {
        // Arrange: mock PublisherRepository
        $publisher = new Publisher(
            'devto',
            'Dev.to',
            [new PublisherConfig('api_key', 'secret')]
        );

        $publisherRepo = $this->createMock(PublisherRepositoryInterface::class);
        $publisherRepo->method('all')->willReturn([$publisher]);

        // Arrange: mock SettingsRepository
        $settingsRepo = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepo->method('get')->with('xpub_publisher_targets', [])->willReturn(['devto']);

        // Act: service call
        $service = new PublisherSettingsService($publisherRepo, $settingsRepo);
        $result = $service->getSettingsViewData();

        // Assert: expected structure
        $this->assertArrayHasKey('publishers', $result);
        $this->assertArrayHasKey('activePublisherSlugs', $result);

        $this->assertCount(1, $result['publishers']);
        $this->assertSame('devto', $result['publishers'][0]['slug']);
        $this->assertSame('Dev.to', $result['publishers'][0]['name']);
        $this->assertTrue($result['publishers'][0]['active']);
        $this->assertSame(['api_key' => 'secret'], $result['publishers'][0]['config']['default']);

        $this->assertSame(['devto'], $result['activePublisherSlugs']);
    }

    public function testSaveSettingsPersistsData(): void
    {
        $publisherRepo = $this->createMock(PublisherRepositoryInterface::class);
        $settingsRepo = $this->createMock(SettingsRepositoryInterface::class);

        $publisherRepo->expects($this->once())
            ->method('updateConfig')
            ->with('devto', ['api_key' => 'secret']);
        $settingsRepo->expects($this->once())
            ->method('set')
            ->with('xpub_publisher_targets', ['devto']);

        $service = new PublisherSettingsService($publisherRepo, $settingsRepo);
        $service->saveSettings(['devto'], ['devto' => ['api_key' => 'secret']]);
    }
}
