<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts\OAuth;

use League\OAuth2\Client\Token\AccessTokenInterface;

interface OAuthTokenProviderInterface
{
    public function getAccessToken(): ?string;

    public function refreshToken(): bool;

    public function storeAccessToken(AccessTokenInterface $accessToken): void;

    public function getAuthorizationUrl(): string;

    public function getState(): string;

    public function fetchAccessTokenByCode(string $code): AccessTokenInterface;

    public function hasRefreshToken(): bool;

    public function shouldRefreshToken(): bool;
}
