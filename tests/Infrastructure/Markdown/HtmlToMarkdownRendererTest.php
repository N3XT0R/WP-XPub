<?php

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRenderer;

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
