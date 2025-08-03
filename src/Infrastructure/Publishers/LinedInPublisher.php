<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Publishers\Contracts\SupportsOAuthFactoryInterface;
use N3XT0R\XPub\Domain\Publishers\Traits\SupportsOAuthFactoryTrait;

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
        $accessToken = $provider->getAccessToken();

        if (empty($accessToken)) {
            $this->error('Missing or invalid access token for LinkedIn.');
            return false;
        }

        $author = $this->getLinkedInAuthorUrn($accessToken);
        if (!$author) {
            $this->error('Could not determine LinkedIn author URN.');
            return false;
        }

        $payload = [
            'author' => $author,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $article->title."\n\n".$article->url,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        $response = wp_remote_post(self::API_ENDPOINT, [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
            'body' => json_encode($payload),
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            $this->error('LinkedIn request failed: '.$response->get_error_message());
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 201) {
            $body = wp_remote_retrieve_body($response);
            $errorMsg = json_decode($body, true)['message'] ?? $body;
            $this->error("LinkedIn response ($code): $errorMsg");
            return false;
        }

        $this->log('Article successfully published to LinkedIn.');
        return true;
    }

    private function getLinkedInAuthorUrn(string $accessToken): ?string
    {
        $response = wp_remote_get('https://api.linkedin.com/v2/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            $this->error('Failed to retrieve LinkedIn profile: '.$response->get_error_message());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['id']) ? 'urn:li:person:'.$body['id'] : null;
    }
}
