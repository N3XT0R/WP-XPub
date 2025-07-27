<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface ViewInterface
{
    public static function render(string $view, array $data = []): void;

    public static function partial(string $view, array $data = []): void;

    public static function slot(callable $content): void;
}