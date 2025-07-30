<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Application\Factory;

use N3XT0R\XPub\Application\Factory\ArticleFactory;
use N3XT0R\XPub\Domain\Entity\Article;
use PHPUnit\Framework\TestCase;

final class ArticleFactoryTest extends TestCase
{
    public function testFromArrayAndToArray(): void
    {
        $data = [
            'postId' => 5,
            'post_parent' => 0,
            'title' => 'Title',
            'content' => 'Body',
            'excerpt' => 'ex',
            'url' => 'http://url',
            'htmlContent' => '<p>html</p>',
            'tags' => ['t1', 't2'],
            'scheduledAt' => new \DateTimeImmutable('2024-01-01 12:00:00'),
        ];

        $factory = new ArticleFactory();
        $article = $factory->fromArray($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame('Title', $article->title);
        $this->assertSame('Body', $article->content);
        $this->assertEquals($data['scheduledAt'], $article->scheduledAt);
        $this->assertSame($data, $factory->toArray($article));
    }

    public function testMissingOptionalFields(): void
    {
        $data = [
            'postId' => 9,
            'title' => 'T',
            'content' => 'C',
            'scheduledAt' => null,
        ];

        $factory = new ArticleFactory();
        $article = $factory->fromArray($data);

        $array = $factory->toArray($article);
        $this->assertSame(9, $array['postId']);
        $this->assertNull($array['excerpt']);
        $this->assertSame([], $array['tags']);
    }
}
