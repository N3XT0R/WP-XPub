<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Config\oAuth\OAuthProviderConfigBuilder;
use N3XT0R\XPub\Domain\Config\PurposeType;
use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Infrastructure\OAuth\Support\GrantTypeResolver;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use RuntimeException;

final class OAuthTokenProviderFactory
{
    protected static array $defaultProviderMap = [
        'mastodon' => \N3XT0R\XPub\Infrastructure\OAuth\Provider\MastodonOAuthTokenProvider::class,
    ];

    public static function createFromPublisherSlug(string $slug): OAuthTokenProviderInterface
    {
        $repository = new PublisherRepository();
        $publisher = $repository->findBySlug($slug, PurposeType::OAUTH);

        if (!$publisher) {
            throw new RuntimeException("Publisher '$slug' not found");
        }

        return self::create($slug, $publisher->getConfigArray());
    }

    protected static function getProviderMap(): array
    {
        return apply_filters('wp_xpub_oauth_provider_map', self::$defaultProviderMap);
    }

    public static function create(string $slug, array $config): OAuthTokenProviderInterface
    {
        $map = self::getProviderMap();

        if (!isset($map[$slug]) || !class_exists($map[$slug])) {
            throw new RuntimeException("No OAuthTokenProvider found for slug '$slug'");
        }

        $class = $map[$slug];

        if (!is_subclass_of($class, OAuthTokenProviderInterface::class)) {
            throw new RuntimeException("Class '$class' must implement OAuthTokenProviderInterface");
        }

        $providerConfig = OAuthProviderConfigBuilder::build($config);
        $grantType = GrantTypeResolver::resolve($config);

        return new $class(
            new GenericProvider($providerConfig),
            new WordpressSettingsRepository(),
            $grantType
        );
    }
}
