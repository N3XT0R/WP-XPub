<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

class MastodonOAuthTokenProvider extends AbstractOAuthTokenProvider
{
    private GenericProvider $provider;

    public function __construct(GenericProvider $provider, SettingsRepositoryInterface $settings)
    {
        parent::__construct($settings, 'mastodon');
        $this->provider = $provider;
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
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['context' => $e]);
            return false;
        }
    }
}