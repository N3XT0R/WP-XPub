<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Support\OAuthPublisherSeederHelper;
use wpdb;

class Migration_5 extends AbstractMigration
{
    protected function install(wpdb $wpdb): void
    {
        OAuthPublisherSeederHelper::upsert('linkedin', 'LinkedIn', [
            'grant_type' => 'authorization_code',
            'clientId' => '',
            'clientSecret' => '',
            'urlAccessToken' => 'https://www.linkedin.com/oauth/v2/accessToken',
            'urlAuthorize' => 'https://www.linkedin.com/oauth/v2/authorization',
            'urlResourceOwnerDetails' => 'https://api.linkedin.com/v2/me',
            'scopes' => 'w_member_social profile',
            'redirectUri' => get_rest_url(null, '/xpub/v1/oauth/linkedin/callback'),
        ]);
    }

    protected function uninstall(wpdb $wpdb): void
    {
        OAuthPublisherSeederHelper::unregister('linkedin');
    }
}
