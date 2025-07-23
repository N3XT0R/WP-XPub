<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

final class SettingsPageRegistrar
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
        echo '<div class="wrap"><h1>XPUB Einstellungen</h1>';
        echo '<p>Hier kommen deine Formulareinstellungen oder React App hin.</p>';
        echo '</div>';
    }
}
