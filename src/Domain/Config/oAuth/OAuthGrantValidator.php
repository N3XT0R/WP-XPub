<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config\oAuth;

use http\Exception\InvalidArgumentException;
use N3XT0R\XPub\Domain\Config\ConfigValidatorInterface;

final class OAuthGrantValidator implements ConfigValidatorInterface
{
    public function supports(array $config): bool
    {
        return isset($config['grant_type']);
    }

    public function validate(array $config): void
    {
        $grantType = $config['grant_type'] ?? 'authorization_code';

        $required = match ($grantType) {
            'client_credentials' => ['clientId', 'clientSecret', 'urlAccessToken'],
            'authorization_code' => ['clientId', 'clientSecret', 'redirectUri', 'urlAuthorize', 'urlAccessToken'],
            default => throw new InvalidArgumentException("Unsupported grant_type: $grantType"),
        };

        $missing = array_filter(
            $required,
            fn(string $key) => !array_key_exists($key, $config)
        );

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                "Missing keys for grant_type '$grantType': ".implode(', ', $missing)
            );
        }
    }
}
