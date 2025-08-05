<?php

namespace N3XT0R\XPub\Infrastructure\DI\Cache;

use N3XT0R\XPub\Domain\Contracts\ClearContainerCacheInterface;
use N3XT0R\XPub\Shared\Plugin\PluginContext;

final readonly class ContainerCacheCleaner implements ClearContainerCacheInterface
{
    public function __construct(
        private PluginContext $context
    ) {
    }

    public function clear(): void
    {
        $cacheDir = dirname($this->context->pluginFile).'/cache/container/compiled/';
        foreach (glob($cacheDir.'*.php') as $file) {
            @unlink($file);
        }
    }
}
