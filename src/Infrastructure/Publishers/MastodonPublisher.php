<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;

class MastodonPublisher extends PublisherAbstract
{
    private const API_ENDPOINT = 'https://mastodon.social/api/v1/statuses';

    protected function handlePublish(Article $article): bool
    {
        $provider = OAuthTokenProviderFactory::createFromPublisherSlug('mastadon');
        $token = $provider->getAccessToken();

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