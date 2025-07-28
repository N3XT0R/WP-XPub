<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Factory;


use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\Factory\WordpressArticleFactoryInterface;
use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use WP_Post;

final class ArticleFactory implements ArticleFactoryInterface, WordpressArticleFactoryInterface
{
    public function __construct(
        private readonly RendersPostContentInterface $renderer
    ) {
    }

    public function fromArray(array $data): Article
    {
        return new Article(
            postId: (int)($data['postId'] ?? 0),
            title: (string)($data['title'] ?? ''),
            content: (string)($data['content'] ?? ''),
            excerpt: $data['excerpt'] ?? null,
            url: (string)($data['url'] ?? ''),
            htmlContent: (string)($data['htmlContent'] ?? ''),
            tags: (array)($data['tags'] ?? []),
        );
    }

    public function fromWpPost(WP_Post $post): Article
    {
        $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);

        return $this->fromArray([
            'postId' => $post->ID,
            'title' => $post->post_title ?? '',
            'content' => $post->post_content ?? '',
            'excerpt' => get_post_meta($post->ID, '_xpub_custom_excerpt', true) ?: $post->post_excerpt ?: null,
            'url' => get_permalink($post),
            'htmlContent' => $this->renderer->render($post),
            'tags' => $tags,
        ]);
    }
}
