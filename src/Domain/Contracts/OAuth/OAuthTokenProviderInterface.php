<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts\OAuth;

interface OAuthTokenProviderInterface
{
    public function getAccessToken(): ?string;

    public function refreshToken(): bool;
}