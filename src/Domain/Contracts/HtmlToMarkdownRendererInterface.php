<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface HtmlToMarkdownRendererInterface
{
    /**
     * Converts HTML input to Markdown.
     */
    public function convert(string $html): string;
}
