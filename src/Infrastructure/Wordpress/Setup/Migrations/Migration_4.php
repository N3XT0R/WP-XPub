<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Support\OAuthPublisherSeederHelper;
use wpdb;

class Migration_4 extends AbstractMigration
{
    protected function install(WPDB $wpdb): void
    {
        OAuthPublisherSeederHelper::register('mastodon', 'Mastodon', [
            'grant_type' => 'authorization_code',
            'clientId' => '',
            'clientSecret' => '',
            'urlAccessToken' => 'https://mastodon.social/oauth/token',
            'urlAuthorize' => 'https://mastodon.social/oauth/authorize',
            'urlResourceOwnerDetails' => 'https://mastodon.social/oauth/userinfo',
            'scopes' => 'profile write:statuses',

        ]);
    }

    protected function uninstall(WPDB $wpdb): void
    {
        OAuthPublisherSeederHelper::unregister('mastodon');
    }

}