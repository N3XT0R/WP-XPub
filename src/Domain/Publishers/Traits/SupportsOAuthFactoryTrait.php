<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Publisher\Traits;

use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;

trait SupportsOAuthFactoryTrait
{
    private ?OAuthTokenProviderFactory $oauthTokenProviderFactory = null;

    public function setOAuthTokenProviderFactory(OAuthTokenProviderFactory $factory): void
    {
        $this->oauthTokenProviderFactory = $factory;
    }

    public function getOAuthTokenProviderFactory(): ?OAuthTokenProviderFactory
    {
        return $this->oauthTokenProviderFactory;
    }
}
