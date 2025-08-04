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
            throw new \RuntimeException('OAuthTokenProviderFactory not available for LinkedIn.');
        }

        $provider = $factory->createFromPublisherSlug('linkedin');
        $accessToken = $provider->getAccessToken();

        if (empty($accessToken)) {
            throw new \RuntimeException('Missing or invalid access token for LinkedIn.');
        }

        $author = $this->getLinkedInAuthorUrn($accessToken);
        if (!$author) {
            throw new \RuntimeException('Could not determine LinkedIn author URN.');
        }

        $payload = [
            'author' => $author,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $article->excerpt,
                    ],
                    'shareMediaCategory' => 'ARTICLE',
                    'media' => [
                        [
                            'status' => 'READY',
                            'description' => ['text' => $article->excerpt],
                            'originalUrl' => $article->url,
                            'title' => [
                                'text' => $article->title,
                            ],
                        ],
                    ],
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
            throw new \RuntimeException('LinkedIn request failed: '.$response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 201) {
            $body = wp_remote_retrieve_body($response);
            $errorMsg = json_decode($body, true)['message'] ?? $body;
            throw new \RuntimeException("LinkedIn response: ".$errorMsg, $code);
        }

        $this->log('Article successfully published to LinkedIn.');
        return true;
    }

    private function getLinkedInAuthorUrn(string $accessToken): ?string
    {
        $response = wp_remote_get('https://api.linkedin.com/v2/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
            ],
            'timeout' => 10,
        ]);


        if (is_wp_error($response)) {
            throw new \RuntimeException('Failed to retrieve LinkedIn profile: '.$response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['sub']) ? 'urn:li:person:'.$body['sub'] : null;
    }
}
