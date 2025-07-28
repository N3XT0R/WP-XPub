<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\WordpressPublisherFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterDispatcher;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class WordpressPublisherFactoryTest extends TestCase
{
    public function testRuntimeExceptionTriggersLogger(): void
    {
        PublisherFactory::setFilterDispatcher(new WordpressFilterDispatcher());
        $publisher = new Publisher('test', 'test', []);

        $repoMock = $this->createMock(PublisherRepository::class);
        $repoMock->method('all')->willReturn([$publisher]);

        $settingsMock = $this->createMock(WordpressSettingsRepository::class);
        $settingsMock->method('get')->willReturn(['test']);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains("class '' not found"),
                $this->arrayHasKey('exception')
            );

        $factory = new WordpressPublisherFactory($repoMock, $settingsMock, $loggerMock);
        $factory->create();
    }
}