<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

use N3XT0R\XPub\Domain\Entity\Article;

interface PublisherInterface
{
    public function publish(Article $article): bool;
}
