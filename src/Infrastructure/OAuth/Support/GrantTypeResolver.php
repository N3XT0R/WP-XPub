<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Support;

final class GrantTypeResolver
{
    public const AUTHORIZATION_CODE = 'authorization_code';
    public const CLIENT_CREDENTIALS = 'client_credentials';

    protected const ALLOWED_TYPES = [
        self::AUTHORIZATION_CODE,
        self::CLIENT_CREDENTIALS,
    ];

    public static function resolve(array $config): string
    {
        if (!empty($config['grantType']) && in_array($config['grantType'], self::ALLOWED_TYPES, true)) {
            return $config['grantType'];
        }

        if (empty($config['redirectUri']) || empty($config['urlAuthorize'])) {
            return self::CLIENT_CREDENTIALS;
        }

        // Fallback zu authorization_code
        return self::AUTHORIZATION_CODE;
    }

    public static function isValid(string $type): bool
    {
        return in_array($type, self::ALLOWED_TYPES, true);
    }
}
