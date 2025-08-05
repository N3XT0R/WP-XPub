<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config;

final class PurposeType
{
    public const DEFAULT = 'default';
    public const OAUTH = 'oauth';

    public static function isValid(string $value): bool
    {
        return in_array($value, [self::DEFAULT, self::OAUTH], true);
    }

    public static function all(): array
    {
        return [
            self::DEFAULT,
            self::OAUTH,
        ];
    }
}
