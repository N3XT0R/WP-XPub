<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use LogicException;
use N3XT0R\XPub\Domain\Contracts\ConfigurablePublisherInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Contracts\SlugAwareInterface;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Infrastructure\DI\ContainerProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use RuntimeException;

final class PublisherFactory
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

    public static function createWithConfig(string $target, array $config): PublisherInterface
    {
        $map = self::getPublisherMap();

        if (!isset($map[$target]) || !class_exists($map[$target])) {
            throw new RuntimeException("No valid publisher class found for target '$target'");
        }

        return self::instantiatePublisher($target, $map[$target], $config);
    }

    public static function all(): array
    {
        $map = self::getPublisherMap();
        $instances = [];

        foreach ($map as $key => $class) {
            if (!class_exists($class)) {
                continue;
            }

            $instance = self::instantiatePublisher($key, $class);
            $instances[$key] = $instance;
        }

        return $instances;
    }

    private static function getPublisherMap(): array
    {
        if (!self::$filterDispatcher) {
            throw new LogicException('FilterDispatcher not set');
        }

        return self::$filterDispatcher->filter(
            'wp_xpub_factory_map',
            self::getDefaultPublisherArray()
        );
    }

    private static function getDefaultPublisherArray(): array
    {
        return [
            'devto' => DevToPublisher::class,
            //'mastodon' => MastodonPublisher::class,
        ];
    }

    private static function instantiatePublisher(string $slug, string $class, array $config = []): PublisherInterface
    {
        $instance = ContainerProvider::getContainer()->get($class);

        if (!$instance instanceof PublisherInterface) {
            throw new RuntimeException("Class '$class' must implement PublisherInterface");
        }

        if ($instance instanceof ConfigurablePublisherInterface) {
            $instance->setConfig($config);
            $instance->setLogger(LoggerFactory::create($slug));
        }

        if ($instance instanceof SlugAwareInterface) {
            $instance->setSlug($slug);
        }

        return $instance;
    }
}
