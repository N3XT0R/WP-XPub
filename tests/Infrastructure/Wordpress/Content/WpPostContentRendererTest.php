<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Content;

use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use PHPUnit\Framework\TestCase;
use WP_Post;

function apply_filters($tag, $value)
{
    return strtoupper($value);
}

function setup_postdata($post)
{
    $GLOBALS['setup_called'] = $post->ID;
}

function wp_reset_postdata()
{
    $GLOBALS['reset_called'] = true;
}

function wp_kses_post($content)
{
    return strip_tags($content);
}

class WpPostContentRendererTest extends TestCase
{
    public function testRenderProcessesContent(): void
    {
        $renderer = new WpPostContentRenderer();
        $post = new WP_Post(['ID' => 5, 'post_content' => '<b>content</b>']);
        $result = $renderer->render($post);

        $this->assertSame('<b>content</b>', $result);
        $this->assertSame(5, $GLOBALS['setup_called']);
        $this->assertTrue($GLOBALS['reset_called']);
    }

    public function testRenderReturnsEmptyStringForEmptyContent(): void
    {
        $renderer = new WpPostContentRenderer();
        $post = new WP_Post(['ID' => 6, 'post_content' => '']);
        $this->assertSame('', $renderer->render($post));
    }
}
