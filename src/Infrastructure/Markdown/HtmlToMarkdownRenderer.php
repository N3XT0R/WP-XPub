<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Markdown;

use League\HTMLToMarkdown\HtmlConverter;
use N3XT0R\XPub\Domain\Contracts\HtmlToMarkdownRendererInterface;

class HtmlToMarkdownRenderer implements HtmlToMarkdownRendererInterface
{
    public function __construct(
        private readonly HtmlConverter $converter = new HtmlConverter()
    ) {
    }

    public function convert(string $html): string
    {
        return $this->converter->convert($html);
    }
}