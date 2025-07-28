<?php

namespace N3XT0R\XPub\Tests\Domain\Entity;

use N3XT0R\XPub\Domain\Entity\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testCanCreateArticle(): void
    {
        $article = new Article(1, 'Title', 'Content');
        $this->assertSame(1, $article->postId);
        $this->assertSame('Title', $article->title);
        $this->assertSame('Content', $article->content);
    }

    public function testRequiresTitleAndContent(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Article(1, '', '');
    }
}
