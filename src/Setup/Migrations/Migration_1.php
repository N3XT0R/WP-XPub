<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Setup\Migrations;

use wpdb;

class Migration_1 extends AbstractMigration
{
    protected function install(wpdb $wpdb): void
    {
        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';
        $logsTable = $wpdb->prefix.'xpub_logs';

        $sql = "
            CREATE TABLE $logsTable (
                id INT AUTO_INCREMENT PRIMARY KEY,
                channel VARCHAR(50),
                level INT,
                level_name VARCHAR(20),
                message TEXT,
                context TEXT,
                timestamp DATETIME
            );
        ";
        dbDelta($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS {$publisherTable} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug_unique (slug)
        );

        CREATE TABLE  IF NOT EXISTS {$configTable} (
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
        );
        ";

        dbDelta($sql);
    }

    protected function uninstall(wpdb $wpdb): void
    {
        $configsTable = $wpdb->prefix.'xpub_configs';
        $publishersTable = $wpdb->prefix.'xpub_publishers';

        $wpdb->query("DROP TABLE IF EXISTS $configsTable");
        $wpdb->query("DROP TABLE IF EXISTS $publishersTable");
    }

}