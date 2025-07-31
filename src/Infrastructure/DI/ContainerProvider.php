<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\DI;

use DI\Container;
use DI\ContainerBuilder;

final class ContainerProvider
{
    private static ?Container $container = null;

    public static function getContainer(): Container
    {
        if (self::$container === null) {
            $dir = dirname(__DIR__, 3);
            $cache = $dir.'/cache/';
            $builder = new ContainerBuilder();
            $builder->addDefinitions($dir.'/config/di.php');

            if (!in_array(wp_get_environment_type(), ['local', 'development'], true)) {
                $builder->writeProxiesToFile(true, $cache.'/cache/proxy/');
                $builder->enableCompilation($cache.'/cache/');
            }
            
            self::$container = $builder->build();
        }

        return self::$container;
    }
}
