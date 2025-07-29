<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\OAuth\OAuthTokenProviderInterface;
use N3XT0R\XPub\Domain\Entity\Article;

class MastodonPublisher extends PublisherAbstract
{
    private const API_ENDPOINT = 'https://mastodon.social/api/v1/statuses';

    private OAuthTokenProviderInterface $tokenProvider;

    public function __construct(OAuthTokenProviderInterface $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    protected function handlePublish(Article $article): bool
    {
        $token = $this->tokenProvider->getAccessToken();

        if (empty($token)) {
            $this->error('Missing or invalid access token for Mastodon.');
            return false;
        }

        $status = $article->title."\n\n".$article->url;

        $response = wp_remote_post(self::API_ENDPOINT, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'status' => $status,
                'visibility' => 'public',
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            $this->error('Request failed: '.$response->get_error_message());
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $this->error("Mastodon response ($code): $body");
            return false;
        }

        $this->log('Article successfully published to Mastodon.');
        return true;
    }
}