<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use N3XT0R\XPub\Adapter\WordpressCron;
use wpdb;

class Migration_2 extends AbstractMigration
{
    protected function install(WPDB $wpdb): void
    {
        $table = $wpdb->prefix.'xpub_queue';
        dbDelta(
            "
            CREATE TABLE IF NOT EXISTS {$table} (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `post_id` INT NOT NULL,
              `publisher` VARCHAR(100) NOT NULL,
              `payload` LONGTEXT NOT NULL,
              `scheduled_at` DATETIME NOT NULL,
              `attempts` INT DEFAULT 0,
              `status` ENUM('pending','done','failed') DEFAULT 'pending',
              `last_error` TEXT NULL,
              `created_at` DATETIME NOT NULL,
              `updated_at` DATETIME NOT NULL,
              PRIMARY KEY (`id`)
            );
        "
        );

        WordpressCron::schedule();
    }

    protected function uninstall(WPDB $wpdb): void
    {
        $prefix = $wpdb->prefix;
        $wpdb->query("DROP TABLE IF EXISTS {$prefix}xpub_queue");
    }

}