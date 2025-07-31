<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\DI;

use DI\Container;
use DI\ContainerBuilder;
use N3XT0R\XPub\Application\Update\ReleaseService;
use N3XT0R\XPub\Infrastructure\Wordpress\Updater\PluginUpdateManager;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;

use function DI\factory;

class InfrastructureContainerConfigurator implements ContainerConfiguratorInterface
{
    private string $pluginFile;
    private string $pluginSlug;
    private string $pluginInfoUrl;

    public function __construct(string $pluginFile, string $pluginSlug, string $pluginInfoUrl)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginSlug = $pluginSlug;
        $this->pluginInfoUrl = $pluginInfoUrl;
    }

    public function configure(ContainerBuilder $builder): void
    {
        $pluginBasename = function_exists('plugin_basename')
            ? plugin_basename($this->pluginFile)
            : basename($this->pluginFile);

        $pluginSlug = $this->pluginSlug;
        $pluginInfoUrl = $this->pluginInfoUrl;

        $builder->addDefinitions([
            PluginUpdateManager::class => factory(
                function (Container $container) use ($pluginBasename, $pluginSlug, $pluginInfoUrl) {
                    return new PluginUpdateManager(
                        $pluginBasename,
                        $pluginSlug,
                        $pluginInfoUrl,
                        $container->get(ReleaseService::class)
                    );
                }
            ),
        ]);
    }
}