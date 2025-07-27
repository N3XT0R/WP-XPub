<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations;

use Closure;
use Monolog\Logger;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use wpdb as WPDB;

abstract class AbstractMigration
{
    protected WPDB $wpdb;
    protected Logger $logger;

    public function __construct(?WPDB $customWpdb = null, ?Logger $logger = null)
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
        }

        $this->wpdb = $customWpdb ?? Database::get();
        $this->logger = $logger ?? LoggerFactory::create();
    }

    /**
     * Run installation queries.
     */
    abstract protected function install(WPDB $wpdb): void;

    /**
     * Run uninstallation queries.
     */
    abstract protected function uninstall(WPDB $wpdb): void;

    /**
     * Execute install within a transaction if supported.
     */
    public function executeInstall(): bool
    {
        return $this->transactionalQueries(fn() => $this->install($this->wpdb));
    }

    /**
     * Execute uninstall within a transaction if supported.
     */
    public function executeUninstall(): bool
    {
        return $this->transactionalQueries(fn() => $this->uninstall($this->wpdb));
    }

    /**
     * Executes queries in a transaction-safe way (if supported).
     */
    protected function transactionalQueries(Closure $callback): bool
    {
        $result = false;
        $usedTransaction = false;

        try {
            if ($this->wpdb->has_cap('transactions')) {
                $this->wpdb->query('START TRANSACTION');
                $usedTransaction = true;
            }

            $callback();

            if ($usedTransaction) {
                $this->wpdb->query('COMMIT');
            }

            $result = true;
        } catch (\Throwable $e) {
            if ($usedTransaction) {
                $this->wpdb->query('ROLLBACK');
            }

            $message = 'Migration failed: '.$e->getMessage();
            $context = ['exception' => $e];

            if (defined('WP_DEBUG') && WP_DEBUG) {
                $this->logger->error($message, $context);
            } else {
                $this->logger->warning('A database migration failed. Please check the logs.', $context);
            }
        }

        return $result;
    }
}
