<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\View;

use N3XT0R\XPub\Domain\Contracts\ViewInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\I18n\Translator;

final class View implements ViewInterface
{

    private static string $basePath = '';

    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/');
    }

    public static function render(string $view, array $data = []): void
    {
        $data['translator'] = new Translator();
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
        return self::$basePath.'/'.$relativePath;
    }
}
