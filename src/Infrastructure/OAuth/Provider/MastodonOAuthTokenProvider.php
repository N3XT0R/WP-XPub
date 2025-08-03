<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;

class MastodonOAuthTokenProvider extends AbstractOAuthTokenProvider
{
    public function __construct(
        GenericProvider $provider,
        SettingsRepositoryInterface $settings,
        LoggerInterface $logger,
        string $grantType = 'authorization_code',
    ) {
        parent::__construct($provider, $settings, $logger, 'mastodon', $grantType);
    }
}
