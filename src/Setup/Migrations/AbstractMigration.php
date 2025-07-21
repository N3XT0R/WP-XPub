<?php
/**
 * Base class for database migrations used by WP-XPub.
 * Extend this to define your own migrations.
 */

declare(strict_types=1);

namespace XPub\Setup\Migrations;

use wpdb;

abstract class AbstractMigration
{
    /**
     * Run the migration using the given wpdb instance.
     *
     * @param  wpdb  $wpdb  WordPress database access object.
     */
    abstract protected function run(wpdb $wpdb): void;


    public function executeAll(): void
    {
        global $wpdb;
        $this->run($wpdb);
    }
}