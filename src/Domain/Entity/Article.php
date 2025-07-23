<?php

namespace N3XT0R\XPub\Domain\Entity;

final class Article
{
    public function __construct(
        public readonly int $postId,
        public readonly string $title,
        public readonly string $content
    ) {
        if (empty($title) || empty($content)) {
            throw new \InvalidArgumentException('Title and content are required.');
        }
    }
}
