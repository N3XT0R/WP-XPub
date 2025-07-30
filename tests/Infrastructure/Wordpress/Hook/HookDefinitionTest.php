<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Domain\Hook\HookDefinition;
use PHPUnit\Framework\TestCase;

class HookDefinitionTest extends TestCase
{
    public function testItStoresAllPropertiesCorrectly(): void
    {
        $callback = fn($arg) => $arg.' tested';
        $hook = new HookDefinition(
            hookName: 'xpub_test_hook',
            callback: $callback,
            priority: 15,
            acceptedArgs: 2
        );

        $this->assertSame('xpub_test_hook', $hook->hookName);
        $this->assertSame($callback, $hook->callback);
        $this->assertSame(15, $hook->priority);
        $this->assertSame(2, $hook->acceptedArgs);
        $this->assertSame('result tested', ($hook->callback)('result'));
    }

    public function testItUsesDefaultValues(): void
    {
        $callback = fn() => 'default';
        $hook = new HookDefinition('default_hook', $callback);

        $this->assertSame(10, $hook->priority);
        $this->assertSame(1, $hook->acceptedArgs);
    }
}
