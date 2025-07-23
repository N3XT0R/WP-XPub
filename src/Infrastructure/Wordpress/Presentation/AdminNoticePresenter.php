<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Presentation;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

final class AdminNoticePresenter
{

    private SettingsRepositoryInterface $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function showIfAvailable(): void
    {
        if (!is_admin()) {
            return;
        }

        if ($msg = $this->settings->get('xpub_admin_notice')) {
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($msg).'</p></div>';
            $this->settings->delete('xpub_admin_notice');
        }
    }
}
