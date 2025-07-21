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


    /**
     * Executes the migration – but not before paying tribute to the sacred mess that is WordPress.
     *
     * WordPress wouldn't be WordPress without the need to manually include some magical,
     * deeply buried admin file just to access a core function like `dbDelta()`.
     * Autoloading? Namespacing? Consistency? Nah. Let's just sprinkle `require_once` everywhere like it's 2006.
     *
     * Enjoy debugging undocumented behaviors in functions that were probably meant to be temporary a decade ago.
     */
    public function executeAll(): void
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
        }
        global $wpdb;
        $this->run($wpdb);
    }
}