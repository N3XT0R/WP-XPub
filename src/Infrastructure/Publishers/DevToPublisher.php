<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;

class DevToPublisher implements PublisherInterface {
    public function publish(string $title, string $content): bool {
        // Simuliertes Publishing
        error_log("Publishing to Dev.to: $title");
        return true;
    }
}
