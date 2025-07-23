<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Hook\HookRegistrableInterface;
use N3XT0R\XPub\Support\View;

final class SettingsPageRegistrar implements HookRegistrableInterface
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addOptionsPage']);
    }


    public function addOptionsPage(): void
    {
        add_options_page(
            'XPUB Einstellungen',      // Page title
            'XPUB',                    // Menu title
            'manage_options',          // Capability
            'xpub-settings',           // Menu slug
            [self::class, 'renderSettingsPage'] // Callback to render content
        );
    }

    public function renderSettingsPage(): void
    {
        View::render('layouts.admin', [
            'title' => 'XPUB Einstellungen',
            'content' => fn() => View::render('admin.settings-page', ['foo' => 'bar']),
        ]);
    }
}

