<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Publishers\Contracts\SupportsOAuthFactoryInterface;
use N3XT0R\XPub\Domain\Publishers\Traits\SupportsOAuthFactoryTrait;
use N3XT0R\XPub\Infrastructure\OAuth\Provider\LinkedInOAuthTokenProvider;

class LinkedInPublisher extends PublisherAbstract implements SupportsOAuthFactoryInterface
{
    use SupportsOAuthFactoryTrait;

    private const API_ENDPOINT = 'https://api.linkedin.com/v2/ugcPosts';

    protected function handlePublish(Article $article): bool
    {
        $factory = $this->getOAuthTokenProviderFactory();
        if (!$factory) {
            $this->error('OAuthTokenProviderFactory not available for LinkedIn.');
            return false;
        }

        $provider = $factory->createFromPublisherSlug('linkedin');

        if (!$provider instanceof LinkedInOAuthTokenProvider) {
            $this->error('Invalid token provider for LinkedIn.');
            return false;
        }

        $accessToken = $provider->getAccessToken();
        if (empty($accessToken)) {
            $this->error('Missing or invalid LinkedIn access token.');
            return false;
        }

        $authorUrn = $provider->getAuthorUrn();
        if (empty($authorUrn)) {
            $this->error('Missing LinkedIn author URN.');
            return false;
        }

        $statusText = $article->title."\n\n".$article->url;

        $body = json_encode([
            'author' => $authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $statusText,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ]);

        $response = wp_remote_post(self::API_ENDPOINT, [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
            'body' => $body,
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            $this->error('LinkedIn request failed: '.$response->get_error_message());
            return false;
        }

        $status = wp_remote_retrieve_response_code($response);
        if ($status !== 201) {
            $body = wp_remote_retrieve_body($response);
            $message = json_decode($body, true)['message'] ?? $body;
            $this->error("LinkedIn API error ($status): $message");
            return false;
        }

        $this->log('Article successfully published to LinkedIn.');
        return true;
    }
}
