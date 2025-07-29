<?php

namespace N3XT0R\XPub\Domain\Entity;

final class Article
{
    public function __construct(
        public readonly int $postId,
        public readonly int $post_parent = 0,
        public readonly string $title,
        public readonly string $content,
        public readonly ?string $excerpt = null,
        public readonly ?string $url = null,
        public readonly ?string $htmlContent = null,
        public readonly array $tags = [],
        public readonly ?\DateTimeImmutable $scheduledAt = null
    ) {
        if (empty($title) || empty($content)) {
            throw new \InvalidArgumentException('Title and content are required.');
        }
    }
}
