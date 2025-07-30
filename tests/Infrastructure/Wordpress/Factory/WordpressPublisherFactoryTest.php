<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactory;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\WordpressPublisherFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class WordpressPublisherFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        // Set the required dispatcher before each test
        PublisherFactory::setFilterDispatcher(new WordpressFilterDispatcher());
    }

    public function test_it_logs_runtime_exception_when_publisher_factory_fails(): void
    {
        // Arrange: publisher entity, repository mock
        $publisher = new Publisher('test', 'test', []);

        $repositoryMock = $this->createMock(PublisherRepository::class);
        $repositoryMock->method('all')->willReturn([$publisher]);

        $settingsMock = $this->createMock(WordpressSettingsRepository::class);
        $settingsMock->method('get')->willReturn(['test']);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains("No valid publisher class found for target 'test'"),
                $this->callback(function ($context) {
                    return isset($context['exception']) && $context['exception'] instanceof \RuntimeException;
                })
            );

        // Act: create the factory and trigger exception
        $factory = new WordpressPublisherFactory($repositoryMock, $settingsMock, $loggerMock);
        $factory->create();
    }
}
