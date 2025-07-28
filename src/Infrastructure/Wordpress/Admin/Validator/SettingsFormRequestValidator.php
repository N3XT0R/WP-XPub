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
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if (
            !current_user_can('manage_options') ||
            empty($nonce) ||
            !wp_verify_nonce($nonce, $this->nonceAction)
        ) {
            wp_die('Berechtigung verweigert oder ungültige Anfrage.');
        }
    }

}