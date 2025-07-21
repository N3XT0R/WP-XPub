<?php

namespace N3XT0R\XPub\Core;

use N3XT0R\XPub\Contracts\PublisherInterface;

class PublisherFactory {
    public static function create(string $target): PublisherInterface {
        $map = apply_filters('wp_xpub_factory_map', [
            'devto' => \N3XT0R\XPub\Publishers\DevToPublisher::class,
        ]);

        if (!isset($map[$target]) || !class_exists($map[$target])) {
            throw new \RuntimeException("No publisher for target: $target");
        }

        return new $map[$target]();
    }
}
