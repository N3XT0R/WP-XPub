<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Factory;

use N3XT0R\XPub\Domain\Contracts\Factory\ArticleFactoryInterface;
use N3XT0R\XPub\Domain\Entity\Article;

class ArticleFactory implements ArticleFactoryInterface
{
    public function fromArray(array $data): Article
    {
        return new Article(
            postId: (int)($data['postId'] ?? 0),
            post_parent: (int)($data['post_parent'] ?? 0),
            title: (string)($data['title'] ?? ''),
            content: (string)($data['content'] ?? ''),
            excerpt: $data['excerpt'] ?? null,
            url: (string)($data['url'] ?? ''),
            htmlContent: (string)($data['htmlContent'] ?? ''),
            tags: (array)($data['tags'] ?? []),
            scheduledAt: $data['scheduledAt'],
        );
    }

    public function toArray(Article $article): array
    {
        return [
            'postId' => $article->postId,
            'post_parent' => $article->post_parent,
            'title' => $article->title,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'url' => $article->url,
            'htmlContent' => $article->htmlContent,
            'tags' => $article->tags,
            'scheduledAt' => $article->scheduledAt,
        ];
    }
}
