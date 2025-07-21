<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Setup;

use N3XT0R\XPub\Setup\Migrations\AbstractMigration;

/**
 * Executes and manages all available plugin migrations.
 *
 * Handles versioned schema updates and clean uninstallation.
 * Stores the latest installed version using WordPress options API.
 *
 * Because doing schema versioning in WordPress requires pretending
 * that structure, consistency, and maintainability were ever part of the plan.
 */
class SetupRunner
{
    public const OPTION_KEY = 'xpub_db_version';
    public const MIGRATION_NAMESPACE = 'N3XT0R\\XPub\\Setup\\Migrations\\';

    public function install(): void
    {
        $installedVersion = (int)get_option(self::OPTION_KEY, 0);

        $migrations = $this->getAvailableMigrations();
        sort($migrations);

        foreach ($migrations as $version) {
            if ($version > $installedVersion) {
                $className = self::MIGRATION_NAMESPACE.'Migration_'.$version;

                if (class_exists($className)) {
                    $migration = new $className();
                    if ($migration instanceof AbstractMigration) {
                        $migration->executeInstall();
                    }
                }
            }
        }

        if (!empty($migrations)) {
            update_option(self::OPTION_KEY, max($migrations));
        }
    }

    public function uninstall(): void
    {
        $migrations = $this->getAvailableMigrations();
        rsort($migrations); // reverse order for safe teardown

        foreach ($migrations as $version) {
            $className = self::MIGRATION_NAMESPACE.'Migration_'.$version;
            if (!class_exists($className)) {
                error_log("[xPub] Migration class $className not found.");
                break;
            }

            $migration = new $className();
            if ($migration instanceof AbstractMigration) {
                $migration->executeUninstall();
            }
        }

        delete_option(self::OPTION_KEY);
    }

    private function getAvailableMigrations(): array
    {
        $directory = __DIR__.'/Migrations';
        $files = scandir($directory);

        $versions = [];

        foreach ($files as $file) {
            if (preg_match('/Migration_(\d+)\.php$/', $file, $matches)) {
                $versions[] = (int)$matches[1];
            }
        }

        return $versions;
    }
}
