<?php
/**
 * Base class for database migrations used by WP-XPub.
 * Extend this to define your own install() and uninstall() methods.
 *
 * This class exists solely because WordPress decided to make
 * basic infrastructure like `dbDelta()` an opt-in mess buried deep
 * in `wp-admin/includes/upgrade.php`.
 *
 * Autoloading? Namespacing? Consistency? Nah.
 * Just `require_once` your way through the dark ages.
 *
 * You're not building plugins here – you're surviving WordPress.
 */

declare(strict_types=1);

namespace N3XT0R\XPub\Setup\Migrations;

use wpdb;

abstract class AbstractMigration
{
    public function __construct()
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
        }
    }

    abstract protected function install(wpdb $wpdb): void;

    abstract protected function uninstall(wpdb $wpdb): void;

    public function executeInstall(): void
    {
        global $wpdb;
        $this->install($wpdb);
    }

    public function executeUninstall(): void
    {
        global $wpdb;
        $this->uninstall($wpdb);
    }
}