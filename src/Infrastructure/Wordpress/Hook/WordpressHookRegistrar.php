<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Hook;

use N3XT0R\XPub\Adapter\WordpressPlugin;
use N3XT0R\XPub\Infrastructure\Wordpress\Admin\SettingsPageRegistrar;

final class WordpressHookRegistrar
{
    public function register(string $pluginFile): void
    {
        register_activation_hook($pluginFile, [WordpressPlugin::class, 'onActivate']);
        register_uninstall_hook($pluginFile, [WordpressPlugin::class, 'onUninstall']);
        $this->registerActions();
        $this->registerAdminRegistrables();
    }

    private function registerActions(): void
    {
        $provider = new HookProvider();

        foreach ($provider->getHooks() as $hook) {
            add_action($hook->hookName, $hook->callback, $hook->priority, $hook->acceptedArgs);
        }
    }

    private function registerAdminRegistrables(): void
    {
        $registrables = [
            new SettingsPageRegistrar(),
        ];

        foreach ($registrables as $registrable) {
            if ($registrable instanceof HookRegistrableInterface) {
                $registrable->register();
            }
        }
    }
}
