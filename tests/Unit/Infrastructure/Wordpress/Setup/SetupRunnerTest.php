<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Setup;

use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class SetupRunnerTest extends TestCase
{
    public function testGetAvailableMigrations(): void
    {
        $settings = $this->createMock(SettingsRepositoryInterface::class);
        $runner = new SetupRunner(new NullLogger(), $settings);

        $ref = new \ReflectionClass($runner);
        $method = $ref->getMethod('getAvailableMigrations');
        $method->setAccessible(true);
        $versions = $method->invoke($runner);

        $this->assertSame([1, 2, 3], $versions);
    }
}
