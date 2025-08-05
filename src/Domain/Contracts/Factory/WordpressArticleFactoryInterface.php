<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts\Factory;

use N3XT0R\XPub\Domain\Entity\Article;

interface WordpressArticleFactoryInterface
{
    public function fromWpPost(\WP_Post $post): Article;
}
