<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Publishers\Contracts;

use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;

interface SupportsOAuthFactoryInterface
{
    public function setOAuthTokenProviderFactory(OAuthTokenProviderFactory $factory): void;

    public function getOAuthTokenProviderFactory(): ?OAuthTokenProviderFactory;
}
