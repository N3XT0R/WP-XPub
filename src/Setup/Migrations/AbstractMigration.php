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

use wpdb as WPDB;

abstract class AbstractMigration
{
    public function __construct()
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
        }
    }

    abstract protected function install(WPDB $wpdb): void;

    abstract protected function uninstall(WPDB $wpdb): void;

    public function executeInstall(): void
    {
        global $wpdb;
        $this->transactionalQueries($wpdb, function () use ($wpdb) {
            $this->install($wpdb);
        });
    }

    public function executeUninstall(): void
    {
        global $wpdb;
        $this->transactionalQueries($wpdb, function () use ($wpdb) {
            $this->uninstall($wpdb);
        });
    }

    private function transactionalQueries(WPDB $wpdb, \Closure $closure): void
    {
        try {
            if ($wpdb->has_cap('transactions')) {
                $wpdb->query('START TRANSACTION');
                $closure();
                $wpdb->query('COMMIT');
            } else {
                $closure();
            }
        } catch (\Throwable $e) {
            $wpdb->query('ROLLBACK');
            if (defined('WP_DEBUG') && WP_DEBUG) {
                update_option('xpub_admin_notice', 'Migration failed: '.$e->getMessage());
            } else {
                update_option('xpub_admin_notice', 'A database migration failed. Please check the logs.');
                error_log('[xPub Migration] '.$e->getMessage());
            }
        }
    }
}