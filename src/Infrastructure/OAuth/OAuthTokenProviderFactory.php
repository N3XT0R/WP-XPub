<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use RuntimeException;

class OAuthTokenProviderFactory
{
    protected static array $defaultProviderMap = [
        'mastodon' => \N3XT0R\XPub\Infrastructure\OAuth\Provider\MastodonOAuthTokenProvider::class,
    ];

    public static function create(string $slug, array $config): OAuthTokenProviderInterface
    {
        $map = apply_filters('wp_xpub_oauth_provider_map', self::$defaultProviderMap);

        if (!isset($map[$slug]) || !class_exists($map[$slug])) {
            throw new RuntimeException("No OAuthTokenProvider found for slug '$slug'");
        }

        $class = $map[$slug];

        if (!is_subclass_of($class, OAuthTokenProviderInterface::class)) {
            throw new RuntimeException("Class '$class' must implement OAuthTokenProviderInterface");
        }

        $requiredKeys = ['clientId', 'clientSecret', 'redirectUri', 'urlAuthorize', 'urlAccessToken'];
        foreach ($requiredKeys as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Missing required config key: '$key'");
            }
        }

        return new $class(
            new GenericProvider([
                'clientId' => $config['clientId'],
                'clientSecret' => $config['clientSecret'],
                'redirectUri' => $config['redirectUri'],
                'urlAuthorize' => $config['urlAuthorize'],
                'urlAccessToken' => $config['urlAccessToken'],
                'urlResourceOwnerDetails' => $config['urlResourceOwnerDetails'] ?? '',
            ]),
            new WordpressSettingsRepository()
        );
    }
}
