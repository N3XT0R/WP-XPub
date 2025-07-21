<?php
namespace XPub\Publishers;

use XPub\Contracts\PublisherInterface;

class DevToPublisher implements PublisherInterface {
    public function publish(string $title, string $content): bool {
        // Simuliertes Publishing
        error_log("Publishing to Dev.to: $title");
        return true;
    }
}
