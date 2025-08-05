<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts\Factory;

use N3XT0R\XPub\Domain\Entity\Article;

interface ArticleFactoryInterface
{
    public function fromArray(array $data): Article;

    public function toArray(Article $article): array;
}
