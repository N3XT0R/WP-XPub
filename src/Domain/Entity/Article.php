<?php

namespace N3XT0R\XPub\Domain\Entity;

use WP_Post;

final class Article
{
    public function __construct(
        public readonly int $postId,
        public readonly string $title,
        public readonly string $content,
        public readonly ?string $excerpt = null,
        public readonly ?string $url = null
    ) {
        if (empty($title) || empty($content)) {
            throw new \InvalidArgumentException('Title and content are required.');
        }
    }

    public static function fromWpPost(WP_Post $post): self
    {
        return new self(
            postId: $post->ID,
            title: $post->post_title ?? '',
            content: $post->post_content ?? '',
            excerpt: $post->post_excerpt ?: null,
            url: get_permalink($post)
        );
    }
}
