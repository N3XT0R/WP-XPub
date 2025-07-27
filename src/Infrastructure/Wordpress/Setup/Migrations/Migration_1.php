<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Support\PublisherSeederHelper;
use wpdb;

class Migration_1 extends AbstractMigration
{
    protected function install(wpdb $wpdb): void
    {
        $prefix = $wpdb->prefix;

        $publisherTable = $prefix.'xpub_publishers';
        $configTable = $prefix.'xpub_publisher_config';
        $logsTable = $prefix.'xpub_logs';

        // Logging table
        dbDelta(
            "
            CREATE TABLE IF NOT EXISTS {$logsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                channel VARCHAR(50),
                level INT,
                level_name VARCHAR(20),
                message TEXT,
                context TEXT,
                timestamp DATETIME
            );
        "
        );

        // Publisher
        dbDelta(
            "
            CREATE TABLE IF NOT EXISTS {$publisherTable} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                slug VARCHAR(50) NOT NULL,
                name VARCHAR(100) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY slug_unique (slug)
            );"
        );

        //config
        dbDelta(
            "
           CREATE TABLE IF NOT EXISTS {$configTable} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                publisher_id BIGINT UNSIGNED NOT NULL,
                config_key VARCHAR(100) NOT NULL,
                config_value TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (publisher_id) REFERENCES {$publisherTable}(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_publisher (publisher_id),
                INDEX idx_key (config_key),
                UNIQUE KEY publisher_key (publisher_id, config_key)
            );
        "
        );

        // Ensure default publisher is present
        PublisherSeederHelper::upsert('devto', 'Dev.to', ['api_key' => '']);
    }

    protected function uninstall(wpdb $wpdb): void
    {
        $prefix = $wpdb->prefix;

        $wpdb->query("DROP TABLE IF EXISTS {$prefix}xpub_publisher_config");
        $wpdb->query("DROP TABLE IF EXISTS {$prefix}xpub_publishers");
        $wpdb->query("DROP TABLE IF EXISTS {$prefix}xpub_logs");
    }
}
