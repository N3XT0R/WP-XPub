<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Shared\Plugin;

final class PluginContext
{
    public function __construct(
        public readonly string $pluginFile,
        public readonly string $pluginSlug,
        public readonly string $pluginInfoUrl,
        public readonly string $handlePrefix
    ) {
    }
}
