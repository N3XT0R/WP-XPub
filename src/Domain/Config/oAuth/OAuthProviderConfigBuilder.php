<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config\OAuth;

final class OAuthProviderConfigBuilder
{
    public static function build(array $config): array
    {
        $grantType = $config['grant_type'] ?? 'authorization_code';

        $requiredKeys = self::requiredKeysForGrantType($grantType);

        $result = [];
        foreach ($requiredKeys as $key) {
            if (array_key_exists($key, $config)) {
                $result[$key] = $config[$key];
            }
        }

        // optional for authorization_code
        if ($grantType === 'authorization_code' && !empty($config['urlResourceOwnerDetails'])) {
            $result['urlResourceOwnerDetails'] = $config['urlResourceOwnerDetails'];
        }

        if (!empty($config['scopes'])) {
            $result['scopes'] = !is_array($config['scopes']) ? explode(' ', $config['scopes']) : $config['scopes'];
        }

        if (!empty($config['code'])) {
            $result['code'] = $config['code'];
        }

        return $result;
    }

    public static function requiredKeysForGrantType(string $grantType): array
    {
        return match ($grantType) {
            'client_credentials' => [
                'clientId',
                'clientSecret',
                'urlAccessToken',
                'urlResourceOwnerDetails',
                'urlAuthorize'
            ],
            'authorization_code' => ['clientId', 'clientSecret', 'redirectUri', 'urlAuthorize', 'urlAccessToken'],
            default => throw new \InvalidArgumentException("Unsupported grant_type: $grantType"),
        };
    }
}
