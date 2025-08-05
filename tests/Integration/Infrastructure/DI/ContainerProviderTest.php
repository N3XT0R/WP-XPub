<?php

namespace N3XT0R\XPub\Tests\Infrastructure\DI;

use DI\Container;
use N3XT0R\XPub\Infrastructure\DI\ContainerProvider;
use N3XT0R\XPub\Shared\Plugin\PluginContext;
use PHPUnit\Framework\TestCase;

class ContainerProviderTest extends TestCase
{
    public function testReturnsSingletonContainer(): void
    {
        ContainerProvider::setPluginContext(new PluginContext(__FILE__, 'test', 'info', 'test'));
        $c1 = ContainerProvider::getContainer();
        $c2 = ContainerProvider::getContainer();
        $this->assertInstanceOf(Container::class, $c1);
        $this->assertSame($c1, $c2);
    }
}
