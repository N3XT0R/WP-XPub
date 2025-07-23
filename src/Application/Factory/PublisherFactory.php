<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Factory;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher;
use N3XT0R\XPub\Infrastructure\Wordpress\Hook\WordpressFilterAdapter;

class PublisherFactory
{
    public static function create(string $target): PublisherInterface
    {
        $map = (new WordpressFilterAdapter())->apply('wp_xpub_factory_map', [
            'devto' => DevToPublisher::class,
        ]);

        $publisher = new $map[$target]();

        if (!$publisher instanceof PublisherInterface) {
            throw new \RuntimeException("Publisher does not implement PublisherInterface: $target");
        }

        return $publisher;
    }
}
