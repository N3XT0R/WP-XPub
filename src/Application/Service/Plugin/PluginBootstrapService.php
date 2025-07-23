<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Plugin;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Support\Version;

final readonly class PluginBootstrapService
{

    public function __construct(
        private SettingsRepositoryInterface $settings
    ) {
    }


    public function bootstrap(): void
    {
        $currentVersion = Version::get();
        $savedVersion = $this->settings->get('xpub_plugin_version');

        if (!empty($currentVersion) && version_compare($currentVersion, (string)$savedVersion, '>')) {
            (new SetupRunner())->install();
            $this->settings->set('xpub_plugin_version', $currentVersion);
        }
    }
}
