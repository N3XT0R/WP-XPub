<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

interface TranslatesMessagesInterface
{
    public function translate(string $message): string;

    public function translateWithContext(string $message, string $context): string;

    public function translatePlural(string $singular, string $plural, int $count): string;

    public function translateFormatted(string $message, array $params = []): string;
}