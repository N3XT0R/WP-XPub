<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Tests\Stubs\DummyDispatcher;
use PHPUnit\Framework\TestCase;

final class WordpressHookRegistrarTest extends TestCase
{
    public function testItDispatchesAllHooksFromProvider(): void
    {
        $provider = new HookProvider();
        $dispatcher = new DummyDispatcher();
        $registrar = new WordpressHookRegistrar($provider, $dispatcher);

        $reflection = new \ReflectionClass($registrar);
        $method = $reflection->getMethod('registerActions');
        $method->invoke($registrar);

        $this->assertCount(count($provider->getHooks()), $dispatcher->dispatched);
    }
}
