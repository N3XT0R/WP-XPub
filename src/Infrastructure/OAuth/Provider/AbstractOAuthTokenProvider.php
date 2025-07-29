<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

abstract class AbstractOAuthTokenProvider implements OAuthTokenProviderInterface
{
    protected SettingsRepositoryInterface $settings;
    protected string $storageKey;

    private const STORAGE_PREFIX = 'xpub_oauth_';

    public function __construct(SettingsRepositoryInterface $settings, string $storageKey)
    {
        $this->settings = $settings;
        $this->storageKey = self::STORAGE_PREFIX.$storageKey;
    }

    public function getAccessToken(): ?string
    {
        $tokenData = $this->settings->get($this->storageKey);
        if (!is_array($tokenData) || empty($tokenData['access_token'])) {
            return null;
        }

        // Optional: check for expiry
        if (isset($tokenData['expires']) && time() >= (int)$tokenData['expires']) {
            $this->refreshToken();
            $tokenData = $this->settings->get($this->storageKey);
        }

        return $tokenData['access_token'] ?? null;
    }
}
