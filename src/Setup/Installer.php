<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Setup;

use XPub\Setup\Migrations\AbstractMigration;

class Installer
{
    const OPTION_KEY = 'xpub_db_version';
    const MIGRATION_NAMESPACE = 'N3XT0R\\XPub\\Setup\\Migrations\\';

    public function run(): void
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
                        $migration->executeAll();
                    }
                }
            }
        }

        if (!empty($migrations)) {
            update_option(self::OPTION_KEY, max($migrations));
        }
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
