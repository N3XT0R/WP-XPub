<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Domain\Contracts\RendersPostContentInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use PHPUnit\Framework\TestCase;
use WP_Post;

function get_post_meta($postId, $key, $single = false)
{
    if ($key === '_xpub_custom_excerpt') {
        return 'excerpt';
    }
    return '';
}

function get_permalink($post): string
{
    return 'http://example.com/'.$post->ID;
}

function wp_get_post_tags($postId, $args)
{
    return ['one', 'two'];
}

class ArticleFactoryTest extends TestCase
{
    public function testFromWpPostCreatesArticle(): void
    {
        $renderer = $this->createMock(RendersPostContentInterface::class);
        $renderer->method('render')->willReturn('<html></html>');

        $post = new WP_Post([
            'ID' => 1,
            'post_title' => 'Title',
            'post_content' => 'Content',
            'post_excerpt' => 'excerpt',
        ]);

        $factory = new ArticleFactory($renderer);
        $article = $factory->fromWpPost($post);

        $this->assertSame(1, $article->postId);
        $this->assertSame('Title', $article->title);
        $this->assertSame('Content', $article->content);
        $this->assertSame('excerpt', $article->excerpt);
        $this->assertSame('http://example.com/1', $article->url);
        $this->assertSame('<html></html>', $article->htmlContent);
        $this->assertSame(['one', 'two'], $article->tags);
    }
}
