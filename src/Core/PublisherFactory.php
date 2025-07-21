<?php
namespace XPub\Core;

use EchoHook\Contracts\PublisherInterface;
use EchoHook\Publishers\DevToPublisher;

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
