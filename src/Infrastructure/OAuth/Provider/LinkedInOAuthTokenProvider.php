<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use N3XT0R\XPub\Infrastructure\OAuth\Provider\Contracts\LinkedInInterface;
use Psr\Log\LoggerInterface;

class LinkedInOAuthTokenProvider extends AbstractOAuthTokenProvider implements LinkedInInterface
{
    public function __construct(
        GenericProvider $provider,
        SettingsRepositoryInterface $settings,
        LoggerInterface $logger,
        string $grantType = 'authorization_code',
    ) {
        parent::__construct($provider, $settings, $logger, 'linkedin', $grantType);
    }

    public function storeAccessToken(AccessTokenInterface $accessToken): void
    {
        if ($this->grantType === 'client_credentials') {
            return;
        }

        $authorUrn = $this->fetchAuthorUrn($accessToken);

        $this->settings->set($this->storageKey, [
            'access_token' => $accessToken->getToken(),
            'refresh_token' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'author_urn' => $authorUrn,
        ]);
    }

    public function getAuthorUrn(): ?string
    {
        $data = $this->settings->get($this->storageKey);
        return $data['author_urn'] ?? null;
    }

    public function fetchAuthorUrn(AccessTokenInterface $accessToken): ?string
    {
        try {
            $request = $this->provider->getAuthenticatedRequest(
                'GET',
                $this->provider->getResourceOwnerDetailsUrl($accessToken),
                $accessToken
            );

            $response = $this->provider->getResponse($request);
            $body = json_decode((string)$response->getBody(), true);

            if (!empty($body['sub'])) {
                return 'urn:li:person:'.$body['sub'];
            }
        } catch (\Throwable $e) {
            $this->logger->error('LinkedIn fetch failed: '.$e->getMessage(), ['exception' => $e]);
        }

        return null;
    }
}