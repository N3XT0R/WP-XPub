<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;

class DevToPublisher implements PublisherInterface
{
    public function publish(Article $article): bool
    {
        // Simuliertes Publishing
        error_log("Publishing to Dev.to: $article->title $article->content");
        return true;
    }
}
