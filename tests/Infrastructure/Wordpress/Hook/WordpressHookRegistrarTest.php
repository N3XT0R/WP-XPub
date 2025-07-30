<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator\SettingsFormRequestValidator;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Tests\Stubs\DummyDispatcher;
use PHPUnit\Framework\TestCase;

final class WordpressHookRegistrarTest extends TestCase
{
    public function testItDispatchesAllHooksFromProvider(): void
    {
        $dummyPluginFile = 'my-plugin/my-plugin.php';
        $provider = new HookProvider(
            $dummyPluginFile,
            new SettingsSaveHandler(
                new SettingsFormRequestValidator(),
                new WordpressSettingsRepository(),
                new PublisherRepository(),
            )
        );
        $dispatcher = new DummyDispatcher();
        $registrar = new WordpressHookRegistrar($provider, $dispatcher, []);

        $reflection = new \ReflectionClass($registrar);
        $method = $reflection->getMethod('registerActions');
        $method->invoke($registrar);

        $this->assertCount(count($provider->getHooks()), $dispatcher->dispatched);
    }
}
