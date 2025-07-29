<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Service\Plugin;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Infrastructure\Wordpress\Version\Version;
use Psr\Log\LoggerInterface;

class PluginBootstrapService
{
    private LoggerInterface $logger;
    private SettingsRepositoryInterface $settings;

    public function __construct(
        SettingsRepositoryInterface $settings
    ) {
        $this->logger = LoggerFactory::create(); // WP-Adapter
        $this->settings = $settings;
    }

    public function bootstrap(): void
    {
        $currentVersion = Version::get();
        $savedVersion = (string)$this->settings->get('xpub_plugin_version', '0.0.0');

        if (!empty($savedVersion) &&
            !empty($currentVersion) && version_compare(
                $currentVersion,
                $savedVersion,
                '>'
            )) {
            $this->logger->info('Running plugin setup for version '.$currentVersion);
            (new SetupRunner($this->logger, $this->settings))->install();
            $this->settings->set('xpub_plugin_version', $currentVersion);
            $this->logger->info('Setup complete. Version stored: '.$currentVersion);
        }
    }
}