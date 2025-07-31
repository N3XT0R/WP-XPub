<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\DI;

use DI\Container;
use DI\ContainerBuilder;
use N3XT0R\XPub\Application\DI\ApplicationContainerConfigurator;
use N3XT0R\XPub\Domain\DI\DomainContainerConfigurator;

final class ContainerProvider
{
    private static ?Container $container = null;
    private static ?string $pluginFile = null;
    private static ?string $pluginSlug = null;
    private static ?string $pluginInfoUrl = null;

    public static function setPluginMetadata(string $file, string $slug, string $infoUrl): void
    {
        self::$pluginFile = $file;
        self::$pluginSlug = $slug;
        self::$pluginInfoUrl = $infoUrl;
    }

    public static function getContainer(): Container
    {
        if (self::$container !== null) {
            return self::$container;
        }

        if (self::$pluginFile === null || self::$pluginSlug === null || self::$pluginInfoUrl === null) {
            throw new \RuntimeException('Plugin metadata must be set before building the container.');
        }

        $projectRoot = dirname(__DIR__, 3);
        $cacheDir = $projectRoot.'/cache/container';

        $builder = new ContainerBuilder();

        $env = function_exists('wp_get_environment_type')
            ? wp_get_environment_type()
            : 'production';

        if (!in_array($env, ['local', 'development'], true)) {
            $builder->writeProxiesToFile(true, $cacheDir.'/proxies');
            $builder->enableCompilation($cacheDir.'/compiled');
        }

        self::configure($builder);

        self::$container = $builder->build();
        return self::$container;
    }

    private static function configure(ContainerBuilder $builder): void
    {
        $configurators = [
            new ApplicationContainerConfigurator(),
            new DomainContainerConfigurator(),
            new InfrastructureContainerConfigurator(
                self::$pluginFile,
                self::$pluginSlug,
                self::$pluginInfoUrl
            ),
        ];

        foreach ($configurators as $configurator) {
            $configurator->configure($builder);
        }
    }
}

