<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator;

class SettingsFormRequestValidator
{
    private string $nonceAction;

    public function __construct(string $nonceAction = 'xpub_save_settings')
    {
        $this->nonceAction = $nonceAction;
    }

    public function validate(): void
    {
        if (
            !current_user_can('manage_options') ||
            !isset($_POST['_wpnonce']) ||
            !wp_verify_nonce($_POST['_wpnonce'], $this->nonceAction)
        ) {
            wp_die('Berechtigung verweigert oder ungültige Anfrage.');
        }
    }

}