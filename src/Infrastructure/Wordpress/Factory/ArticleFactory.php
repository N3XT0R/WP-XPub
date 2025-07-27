<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use WP_Post;

final class ArticleFactory
{
    public function __construct(
        private readonly RendersPostContentInterface $renderer
    ) {
    }


    public function fromWpPost(WP_Post $post): Article
    {
        $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);

        return new Article(
            postId: $post->ID,
            title: $post->post_title ?? '',
            content: $post->post_content ?? '',
            excerpt: get_post_meta($post->ID, '_xpub_custom_excerpt', true) ?: $post->post_excerpt ?: null,
            url: get_permalink($post),
            htmlContent: $this->renderer->render($post),
            tags: $tags,
        );
    }
}