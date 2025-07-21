<?php

declare(strict_types=1);

namespace XPub\Setup\Migrations;

use wpdb;

class Migration_1 extends AbstractMigration
{
    protected function run(wpdb $wpdb): void
    {
        $charsetCollate = $wpdb->get_charset_collate();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $sql = "
        CREATE TABLE {$publisherTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug_unique (slug)
        ) {$charsetCollate};

        CREATE TABLE {$configTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            publisher_id BIGINT UNSIGNED NOT NULL,
            config_key VARCHAR(100) NOT NULL,
            config_value TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (publisher_id) REFERENCES {$publisherTable}(id) ON DELETE CASCADE ON UPDATE CASCADE,
            INDEX idx_publisher (publisher_id),
            INDEX idx_key (config_key)
        ) {$charsetCollate};
        ";

        dbDelta($sql);
    }

}