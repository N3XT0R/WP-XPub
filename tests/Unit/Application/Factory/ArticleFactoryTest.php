<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Factory;

use N3XT0R\XPub\Application\Factory\ArticleFactory;
use N3XT0R\XPub\Domain\Entity\Article;
use PHPUnit\Framework\TestCase;

final class ArticleFactoryTest extends TestCase
{
    public function testFromArrayCreatesArticle(): void
    {
        $data = [
            'postId' => 5,
            'post_parent' => 1,
            'title' => 'Title',
            'content' => 'Content',
            'excerpt' => 'Excerpt',
            'url' => 'http://example.com',
            'htmlContent' => '<p>HTML</p>',
            'tags' => ['a', 'b'],
            'scheduledAt' => new \DateTimeImmutable('2024-01-01'),
        ];

        $factory = new ArticleFactory();
        $article = $factory->fromArray($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame(5, $article->postId);
        $this->assertSame('Title', $article->title);
        $this->assertSame(['a', 'b'], $article->tags);
        $this->assertEquals(new \DateTimeImmutable('2024-01-01'), $article->scheduledAt);
    }

    public function testToArrayReturnsData(): void
    {
        $factory = new ArticleFactory();
        $article = new Article(1, 0, 't', 'c', 'e', 'http://ex', '<p>c</p>', ['tag'], new \DateTimeImmutable('2024-01-01'));

        $array = $factory->toArray($article);

        $this->assertSame(1, $array['postId']);
        $this->assertSame('t', $array['title']);
        $this->assertSame(['tag'], $array['tags']);
        $this->assertSame('http://ex', $array['url']);
    }
}
