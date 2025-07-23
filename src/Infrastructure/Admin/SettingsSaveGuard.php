<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

final class SettingsSaveGuard
{
    public function ensureAuthorized(): void
    {
        if (
            !current_user_can('manage_options') ||
            !isset($_POST['_wpnonce']) ||
            !wp_verify_nonce($_POST['_wpnonce'], 'xpub_save_settings')
        ) {
            wp_die('Berechtigung verweigert oder ungültige Anfrage.');
        }
    }
}
