<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Support\OAuthPublisherSeederHelper;
use wpdb;

class Migration_4 extends AbstractMigration
{
    protected function install(WPDB $wpdb): void
    {
        OAuthPublisherSeederHelper::upsert('mastodon', 'Mastodon', [
            'grant_type' => 'authorization_code',
            'clientId' => '',
            'urlAccessToken' => 'https://mastodon.social/oauth/token',
            'urlAuthorize' => 'https://mastodon.social/oauth/authorize',
            'urlResourceOwnerDetails' => 'https://mastodon.social/api/v1/accounts/verify_credentials',
            'scopes' => 'profile write:statuses',
            'redirectUri' => get_rest_url(null, '/xpub/v1/oauth/mastodon/callback'),
        ]);
    }

    protected function uninstall(WPDB $wpdb): void
    {
        OAuthPublisherSeederHelper::unregister('mastodon');
    }
}
