<?php

namespace N3XT0R\XPub\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

class PublisherTargetProviderTest extends TestCase
{
    public function testReturnsTargetsArray(): void
    {
        $settings = new class implements SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed {
                return ['devto'];
            }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };

        $provider = new PublisherTargetProvider($settings);
        $this->assertSame(['devto'], $provider->getTargets());
    }

    public function testReturnsEmptyArrayForNonArraySetting(): void
    {
        $settings = new class implements SettingsRepositoryInterface {
            public function get(string $key, mixed $default = null): mixed { return 'invalid'; }
            public function set(string $key, mixed $value): bool { return true; }
            public function delete(string $key): bool { return true; }
        };

        $provider = new PublisherTargetProvider($settings);
        $this->assertSame([], $provider->getTargets());
    }
}
