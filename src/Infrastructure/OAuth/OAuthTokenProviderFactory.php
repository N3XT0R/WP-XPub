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
    /**
     * @param  array<string, class-string<OAuthTokenProviderInterface>>  $providerMap
     */
    public function __construct(
        private PublisherRepository $repository,
        private WordpressSettingsRepository $settingsRepo,
        private array $providerMap = [
            'mastodon' => \N3XT0R\XPub\Infrastructure\OAuth\Provider\MastodonOAuthTokenProvider::class,
        ],
    ) {
    }

    public function createFromPublisherSlug(string $slug, array $mergeConfig = []): OAuthTokenProviderInterface
    {
        $publisher = $this->repository->findBySlug($slug, PurposeType::OAUTH);

        if (!$publisher) {
            throw new RuntimeException("Publisher '$slug' not found");
        }

        $config = array_replace_recursive($publisher->getConfigArray(), $mergeConfig);
        return $this->create($slug, $config);
    }

    /**
     * @return array<string, class-string<OAuthTokenProviderInterface>>
     */
    private function getProviderMap(): array
    {
        return apply_filters('wp_xpub_oauth_provider_map', $this->providerMap);
    }

    public function create(string $slug, array $config): OAuthTokenProviderInterface
    {
        $map = $this->getProviderMap();

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
            $this->settingsRepo,
            $grantType
        );
    }
}
