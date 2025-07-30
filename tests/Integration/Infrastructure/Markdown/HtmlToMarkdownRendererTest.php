<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Markdown;

use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRenderer;
use PHPUnit\Framework\TestCase;

class HtmlToMarkdownRendererTest extends TestCase
{
    public function testConvert(): void
    {
        $renderer = new HtmlToMarkdownRenderer();
        $markdown = $renderer->convert('<h1>Title</h1><p>text</p>');
        $this->assertStringContainsString('# Title', $markdown);
        $this->assertStringContainsString('text', $markdown);
    }
}
