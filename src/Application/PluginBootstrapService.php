<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application;

use N3XT0R\XPub\Infrastructure\Wordpress\Setup\SetupRunner;
use N3XT0R\XPub\Support\Version;

final class PluginBootstrapService
{
    public function bootstrap(): void
    {
        $currentVersion = Version::get();
        $savedVersion = get_option('xpub_plugin_version');

        if (!empty($currentVersion) && version_compare($currentVersion, (string)$savedVersion, '>')) {
            (new SetupRunner())->install();
            update_option('xpub_plugin_version', $currentVersion);
        }

        $publisher = PublisherFactory::create('devto');
        $publisher->publish('Hello World', 'Dies ist ein Testbeitrag.');
    }
}
