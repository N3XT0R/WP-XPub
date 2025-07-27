<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Setup;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;

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
    public const MIGRATION_NAMESPACE = 'N3XT0R\\XPub\\Infrastructure\\Wordpress\\Setup\\Migrations\\';

    private LoggerInterface $logger;
    private SettingsRepositoryInterface $settings;

    public function __construct(LoggerInterface $logger, SettingsRepositoryInterface $settings)
    {
        $this->logger = $logger;
        $this->settings = $settings;
    }

    public function install(): void
    {
        $installedVersion = (int)$this->settings->get(self::OPTION_KEY, 0);

        $migrations = $this->getAvailableMigrations();
        sort($migrations);

        foreach ($migrations as $version) {
            if ($version > $installedVersion) {
                $className = self::MIGRATION_NAMESPACE.'Migration_'.$version;

                if (class_exists($className) && $className !== AbstractMigration::class) {
                    $migration = new $className();
                    if ($migration instanceof AbstractMigration) {
                        $migration->executeInstall();
                    }
                }
            }
        }

        if (!empty($migrations)) {
            $this->settings->set(self::OPTION_KEY, max($migrations));
        }
    }

    public function uninstall(): void
    {
        $migrations = $this->getAvailableMigrations();
        rsort($migrations); // reverse order for safe teardown

        foreach ($migrations as $version) {
            $className = self::MIGRATION_NAMESPACE.'Migration_'.$version;
            if (!class_exists($className)) {
                $this->logger->error("Migration class {$className} not found.");
                break;
            }

            if (class_exists($className) && $className !== AbstractMigration::class) {
                $migration = new $className();
                if ($migration instanceof AbstractMigration) {
                    $migration->executeUninstall();
                }
            }
        }

        $this->settings->delete(self::OPTION_KEY);
    }

    private function getAvailableMigrations(): array
    {
        $directory = __DIR__.'/Migrations';
        $files = scandir($directory);
        if (!is_array($files)) {
            $this->logger->error("Could not read migration directory: {$directory}");
            return [];
        }

        $versions = [];

        foreach ($files as $file) {
            if (preg_match('/Migration_(\d+)\.php$/', $file, $matches)) {
                $versions[] = (int)$matches[1];
            }
        }

        return $versions;
    }
}
