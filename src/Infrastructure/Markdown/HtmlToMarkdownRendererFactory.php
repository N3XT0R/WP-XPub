<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Markdown;

use N3XT0R\XPub\Domain\Contracts\HtmlToMarkdownRendererInterface;

final class HtmlToMarkdownRendererFactory
{
    public static function create(): HtmlToMarkdownRendererInterface
    {
        return new HtmlToMarkdownRenderer();
    }
}
