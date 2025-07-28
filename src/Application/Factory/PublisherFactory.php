<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Factory;

use N3XT0R\XPub\Domain\Contracts\ConfigurablePublisherInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Contracts\SlugAwareInterface;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;

class PublisherFactory
{

    private static ?FilterDispatcherInterface $filterDispatcher = null;

    public static function setFilterDispatcher(FilterDispatcherInterface $dispatcher): void
    {
        self::$filterDispatcher = $dispatcher;
    }

    public static function create(string $target): PublisherInterface
    {
        return self::createWithConfig($target, []);
    }

    private static function getDefaultPublisherArray(): array
    {
        return [
            'devto' => DevToPublisher::class,
        ];
    }


    public static function createWithConfig(string $target, array $config): PublisherInterface
    {
        if (!self::$filterDispatcher) {
            throw new \LogicException('FilterDispatcher not set');
        }

        $map = self::$filterDispatcher->filter('wp_xpub_factory_map', self::getDefaultPublisherArray());


        $class = $map[$target] ?? '';
        if (!class_exists($class)) {
            throw new \RuntimeException("class '$class' not found");
        }

        /** @var PublisherInterface $instance */
        $instance = new $class();

        if ($instance instanceof ConfigurablePublisherInterface) {
            $instance->setConfig($config);
            $instance->setLogger(LoggerFactory::create($target));
        }

        if ($instance instanceof SlugAwareInterface) {
            $instance->setSlug($target);
        }

        if (!$instance instanceof PublisherInterface) {
            throw new \RuntimeException("Publisher class for target '$target' must implement PublisherInterface");
        }

        return $instance;
    }


}
