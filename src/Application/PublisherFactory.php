<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

class PublisherFactory {
    public static function create(string $target): PublisherInterface {
        $map = apply_filters('wp_xpub_factory_map', [
            'devto' => \N3XT0R\XPub\Infrastructure\Publishers\DevToPublisher::class,
        ]);

        if (!isset($map[$target]) || !class_exists($map[$target])) {
            throw new \RuntimeException("No publisher for target: $target");
        }

        return new $map[$target]();
    }
}
