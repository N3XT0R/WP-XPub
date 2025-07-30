<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Domain\Config\PurposeType;
use N3XT0R\XPub\Support\PublisherSeederHelper;
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

        PublisherSeederHelper::upsert('mastodon', 'Mastodon', [
            'clientId' => ['value' => '', 'purpose_type' => PurposeType::DEFAULT],
            'clientSecret' => ['value' => '', 'purpose_type' => PurposeType::DEFAULT],
            'urlAuthorize' => 'https://your.instance/oauth/authorize',
            'urlAccessToken' => 'https://your.instance/oauth/token',
            'urlResourceOwnerDetails' => 'https://your.instance/api/v1/accounts/verify_credentials',
        ]);
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
