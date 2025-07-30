<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Stubs;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;

final class SimplePublisher implements PublisherInterface
{
    public array $published = [];
    public bool $shouldFail = false;

    public function publish(Article $article): bool
    {
        $this->published[] = $article;
        if ($this->shouldFail) {
            throw new \RuntimeException('fail');
        }
        return true;
    }
}
