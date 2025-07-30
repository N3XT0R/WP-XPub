<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookDefinition;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsSaveHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Rest\OAuthController;
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator\SettingsFormRequestValidator;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use PHPUnit\Framework\TestCase;

class HookProviderTest extends TestCase
{
    public function testItProvidesExpectedHooks(): void
    {
        $dummyPluginFile = 'my-plugin/my-plugin.php';
        $provider = new HookProvider(
            $dummyPluginFile,
            new SettingsSaveHandler(
                new SettingsFormRequestValidator(),
                new WordpressSettingsRepository(),
                new PublisherRepository(),
            ),
            new OAuthController(
                new OAuthTokenProviderFactory(
                    new PublisherRepository(),
                    new WordpressSettingsRepository(),
                )
            )
        );
        $hooks = $provider->getHooks();

        $this->assertIsArray($hooks);
        foreach ($hooks as $hook) {
            $this->assertInstanceOf(HookDefinition::class, $hook);
            $this->assertIsString($hook->hookName);
            $this->assertIsCallable($hook->callback);
            $this->assertIsInt($hook->priority);
            $this->assertIsInt($hook->acceptedArgs);
        }

        $hookNames = array_map(fn(HookDefinition $h) => $h->hookName, $hooks);
        $this->assertContains('init', $hookNames);
        $this->assertContains('admin_notices', $hookNames);
        $this->assertContains('save_post', $hookNames);
        $this->assertContains('publish_post', $hookNames);
        $this->assertContains('admin_post_xpub_save_settings', $hookNames);
    }
}
