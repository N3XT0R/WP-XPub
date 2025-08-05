<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Application\Service\Admin\PublisherSettingsService;
use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Infrastructure\DI\Cache\ContainerCacheCleaner;
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator\SettingsFormRequestValidator;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressHookRegistrar;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\SettingsController;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Shared\Plugin\PluginContext;
use N3XT0R\XPub\Tests\Stubs\DummyDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class WordpressHookRegistrarTest extends TestCase
{
    public function testItDispatchesAllHooksFromProvider(): void
    {
        $settingsSaveHandler = new SettingsSaveHandler(
            new SettingsFormRequestValidator(),
            new WordpressSettingsRepository(),
            new PublisherRepository(),
        );

        $settingsController = new SettingsController(
            new PublisherSettingsService(
                new PublisherRepository(),
                new WordpressSettingsRepository()
            ),
            $settingsSaveHandler
        );

        $provider = new HookProvider(
            $settingsSaveHandler,
            new OAuthController(
                new OAuthTokenProviderFactory(
                    new PublisherRepository(),
                    new WordpressSettingsRepository(),
                    new NullLogger()
                ),
                new NullLogger()
            ),
            new PluginUpdateManager(
                $context = new PluginContext(
                    'plugin.php',
                    'xpub-multi-channel-publisher',
                    'https://example.com',
                    'test'
                ),
                new ReleaseService(),
                new ContainerCacheCleaner($context)
            ),
            $settingsController
        );
        $dispatcher = new DummyDispatcher();
        $registrar = new WordpressHookRegistrar($provider, $dispatcher, []);

        $reflection = new \ReflectionClass($registrar);
        $method = $reflection->getMethod('registerActions');
        $method->invoke($registrar);

        $this->assertCount(count($provider->getHooks()), $dispatcher->dispatched);
    }
}
