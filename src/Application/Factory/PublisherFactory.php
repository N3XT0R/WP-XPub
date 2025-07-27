<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Factory;

use N3XT0R\XPub\Domain\Contracts\ConfigurablePublisherInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Contracts\SlugAwareInterface;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;

class PublisherFactory
{

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
        $map = apply_filters('wp_xpub_factory_map', self::getDefaultPublisherArray());

        if (!isset($map[$target]) || !class_exists($map[$target])) {
            throw new \RuntimeException("No publisher for target: $target");
        }

        $class = $map[$target];

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
