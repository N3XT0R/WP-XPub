<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Support;

final class View
{
    private const BASE_PATH = __DIR__.'/../../resources/views';

    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $path = self::resolvePath($view);

        if (!file_exists($path)) {
            throw new \RuntimeException("View [{$view}] not found at [{$path}]");
        }

        include $path;
    }

    public static function partial(string $view, array $data = []): void
    {
        self::render($view, $data);
    }

    public static function slot(callable $content): void
    {
        $content(); // Useful for passing content blocks into layouts
    }

    private static function resolvePath(string $view): string
    {
        $relativePath = str_replace('.', '/', $view).'.php';
        return self::BASE_PATH.'/'.$relativePath;
    }
}
