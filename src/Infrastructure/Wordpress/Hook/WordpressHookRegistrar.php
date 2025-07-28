<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Adapter\WordpressPlugin;
use N3XT0R\XPub\Domain\Hook\HookDispatcherInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\MetaBox;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;

final readonly class WordpressHookRegistrar
{
    public function __construct(
        private HookProvider $provider = new HookProvider(),
        private HookDispatcherInterface $dispatcher = new WordpressHookDispatcher()
    ) {
    }

    public function register(string $pluginFile): void
    {
        register_activation_hook($pluginFile, [WordpressPlugin::class, 'onActivate']);
        //register_deactivation_hook($pluginFile, [WordpressPlugin::class, 'onUninstall']);
        register_uninstall_hook($pluginFile, [WordpressPlugin::class, 'onUninstall']);
        $this->registerActions();
        $this->registerAdminRegistrables();
    }

    private function registerActions(): void
    {
        foreach ($this->provider->getHooks() as $hook) {
            $this->dispatcher->dispatch($hook);
        }
    }

    private function registerAdminRegistrables(): void
    {
        $registrables = [
            new SettingsPageRegistrar(),
            new MetaBox(),
        ];

        foreach ($registrables as $registrable) {
            if ($registrable instanceof HookRegistrableInterface) {
                $registrable->register();
            }
        }
    }
}
