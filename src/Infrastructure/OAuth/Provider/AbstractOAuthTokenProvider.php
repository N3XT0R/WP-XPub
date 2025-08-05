<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractOAuthTokenProvider implements OAuthTokenProviderInterface
{
    protected SettingsRepositoryInterface $settings;
    protected GenericProvider $provider;
    protected LoggerInterface $logger;
    protected string $storageKey;
    protected string $grantType;

    public function __construct(
        GenericProvider $provider,
        SettingsRepositoryInterface $settings,
        LoggerInterface $logger,
        string $storageKey,
        string $grantType = 'authorization_code'
    ) {
        $this->provider = $provider;
        $this->settings = $settings;
        $this->storageKey = $storageKey;
        $this->grantType = $grantType;
        $this->logger = $logger;
    }

    public function getAccessToken(): ?string
    {
        if ($this->grantType === 'client_credentials') {
            $accessToken = $this->provider->getAccessToken('client_credentials');
            return $accessToken->getToken();
        }

        // Default: authorization_code
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
        if ($this->grantType !== 'authorization_code') {
            return false;
        }

        $tokenData = $this->settings->get($this->storageKey);
        if (empty($tokenData['refresh_token'])) {
            return false;
        }

        try {
            $accessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $tokenData['refresh_token'],
            ]);

            $this->storeAccessToken($accessToken);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('OAuth refresh failed: '.$e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public function storeAccessToken(AccessTokenInterface $accessToken): void
    {
        if ($this->grantType === 'client_credentials') {
            return;
        }

        $this->settings->set($this->storageKey, [
            'access_token' => $accessToken->getToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
        ]);
    }

    public function getAuthorizationUrl(array $options = []): string
    {
        return $this->provider->getAuthorizationUrl($options);
    }

    public function getState(): string
    {
        return $this->provider->getState();
    }

    public function fetchAccessTokenByCode(string $code): AccessTokenInterface
    {
        return $this->provider->getAccessToken('authorization_code', ['code' => $code]);
    }

    public function fetchAccessTokenByClientCredentials(): AccessTokenInterface
    {
        return $this->provider->getAccessToken('client_credentials');
    }

    public function hasRefreshToken(): bool
    {
        $tokenData = $this->settings->get($this->storageKey);
        return !empty($tokenData['refresh_token']);
    }

    public function shouldRefreshToken(): bool
    {
        $tokenData = $this->settings->get($this->storageKey);
        if (!is_array($tokenData)) {
            return false;
        }

        $expires = $tokenData['expires'] ?? null;
        if ($expires === null) {
            return false;
        }

        return time() >= ((int)$expires - 60);
    }


}
