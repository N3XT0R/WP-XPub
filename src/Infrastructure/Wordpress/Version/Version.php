<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Version;

class Version
{
    public static function get(): string
    {
        if (function_exists('get_plugin_data')) {
            $pluginData = get_plugin_data(__DIR__.'/../../../../xpub-multi-channel-publisher.php', false, false);
            return $pluginData['Version'] ?? '0.0.0';
        }

        return '0.0.0';
    }
}
