<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use wpdb;

class Migration_3 extends AbstractMigration
{
    protected function install(WPDB $wpdb): void
    {
        $table = $wpdb->prefix.'xpub_publisher_config';

        // Spalte config_type hinzufügen, wenn sie noch nicht existiert
        $columnExists = $wpdb->get_results(
            "
            SHOW COLUMNS FROM {$table} LIKE 'purpose_type'
        "
        );

        if (empty($columnExists)) {
            $wpdb->query(
                "
                ALTER TABLE {$table}
                ADD COLUMN purpose_type  VARCHAR(50) NOT NULL DEFAULT 'default'
                AFTER config_key
            "
            );
        }
        $wpdb->query(
            "
                    ALTER TABLE {$table}
                    MODIFY COLUMN config_value TEXT NOT NULL
                "
        );
        /*
        OAuthPublisherSeederHelper::register('mastodon', 'Mastodon', [
            'grant_type' => 'client_credentials',
            'clientId' => '',
            'clientSecret' => '',
            'urlAccessToken' => 'https://mastodon.social/oauth/token',
            'urlAuthorize' => 'https://mastodon.social/oauth/authorize',
            'urlResourceOwnerDetails' => 'https://mastodon.social/oauth/userinfo',
            'scopes' => 'profile write:statuses',

        ]);*/
    }

    protected function uninstall(WPDB $wpdb): void
    {
        $table = $wpdb->prefix.'xpub_publisher_config';

        // Optional: Spalte beim Uninstall wieder entfernen
        $columnExists = $wpdb->get_results(
            "
            SHOW COLUMNS FROM {$table} LIKE 'purpose_type'
        "
        );

        if (!empty($columnExists)) {
            $wpdb->query(
                "
                ALTER TABLE {$table}
                DROP COLUMN purpose_type 
            "
            );
        }

        $wpdb->query(
            "
                    ALTER TABLE {$table}
                    MODIFY COLUMN config_value VARCHAR(100) NOT NULL
                "
        );
    }
}
