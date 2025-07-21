<?php
namespace XPub\Core;

use XPub\Contracts\PublisherInterface;
use XPub\Publishers\DevToPublisher;

class PublisherFactory {
    public static function create(string $type): PublisherInterface {
        switch ($type) {
            case 'devto':
                return new DevToPublisher();
            default:
                throw new \InvalidArgumentException("Unknown publisher type: $type");
        }
    }
}
