<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Domain\Publishers\Contracts\SupportsOAuthFactoryInterface;
use N3XT0R\XPub\Domain\Publishers\Traits\SupportsOAuthFactoryTrait;

class MastodonPublisher extends PublisherAbstract implements SupportsOAuthFactoryInterface
{

    use SupportsOAuthFactoryTrait;

    private const API_ENDPOINT = 'https://mastodon.social/api/v1/statuses';

    protected function handlePublish(Article $article): bool
    {
        $factory = $this->getOAuthTokenProviderFactory();
        if (!$factory) {
            $this->error('OAuthTokenProviderFactory not available for Mastodon.');
            return false;
        }

        $provider = $factory->createFromPublisherSlug('mastodon');
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
        if ($code !== 200 && $code !== 201) {
            $body = wp_remote_retrieve_body($response);
            $errorMsg = json_decode($body, true)['error'] ?? $body;
            $this->error("Mastodon response ($code): $errorMsg");
            return false;
        }

        $this->log('Article successfully published to Mastodon.');
        return true;
    }
}