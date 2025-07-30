<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Repository;

interface PostStatusRepositoryInterface
{
    /**
     * Determine if a post is published and has no newer unpublished revisions.
     */
    public function isPublishedAndNotOutdated(int $postId): bool;
}
