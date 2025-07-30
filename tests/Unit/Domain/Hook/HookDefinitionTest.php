<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Domain\Hook;

use N3XT0R\XPub\Domain\Hook\HookDefinition;
use PHPUnit\Framework\TestCase;

final class HookDefinitionTest extends TestCase
{
    public function testPropertiesAreSetFromConstructor(): void
    {
        $callback = static fn() => 'ok';
        $hook = new HookDefinition('save_post', $callback, 20, 3);

        $this->assertSame('save_post', $hook->hookName);
        $this->assertSame($callback, $hook->callback);
        $this->assertSame(20, $hook->priority);
        $this->assertSame(3, $hook->acceptedArgs);
    }

    public function testDefaultValues(): void
    {
        $callback = static fn() => null;
        $hook = new HookDefinition('init', $callback);

        $this->assertSame(10, $hook->priority);
        $this->assertSame(1, $hook->acceptedArgs);
    }
}
