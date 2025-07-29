<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractOAuthTokenProvider implements OAuthTokenProviderInterface
{
    protected SettingsRepositoryInterface $settings;
    protected GenericProvider $provider;
    protected LoggerInterface $logger;
    protected string $storageKey;

    public function __construct(
        GenericProvider $provider,
        SettingsRepositoryInterface $settings,
        string $storageKey,
        ?LoggerInterface $logger = null
    ) {
        $this->provider = $provider;
        $this->settings = $settings;
        $this->storageKey = $storageKey;
        $this->logger = $logger ?? LoggerFactory::create();
    }

    public function getAccessToken(): ?string
    {
        $tokenData = $this->settings->get($this->storageKey);
        if (!is_array($tokenData) || empty($tokenData['access_token'])) {
            return null;
        }

        if (isset($tokenData['expires']) && time() >= (int)$tokenData['expires']) {
            $this->refreshToken();
            $tokenData = $this->settings->get($this->storageKey);
        }

        return $tokenData['access_token'] ?? null;
    }

    public function refreshToken(): bool
    {
        $tokenData = $this->settings->get($this->storageKey);
        if (empty($tokenData['refresh_token'])) {
            return false;
        }

        try {
            $accessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $tokenData['refresh_token'],
            ]);

            $this->settings->set($this->storageKey, [
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken() ?? $tokenData['refresh_token'],
                'expires' => $accessToken->getExpires(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('OAuth refresh failed: '.$e->getMessage(), ['exception' => $e]);
            return false;
        }
    }
}
