<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Presentation;

final class AdminNoticePresenter
{
    public function showIfAvailable(): void
    {
        if (!is_admin()) {
            return;
        }

        if ($msg = get_option('xpub_admin_notice')) {
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($msg).'</p></div>';
            delete_option('xpub_admin_notice');
        }
    }
}
