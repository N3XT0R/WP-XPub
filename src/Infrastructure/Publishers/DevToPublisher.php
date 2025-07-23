<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;

class DevToPublisher extends PublisherAbstract
{
    private const API_ENDPOINT = 'https://dev.to/api/articles';

    public function publish(Article $article): bool
    {
        $apiKey = $this->getByKey('api_key');

        if (!$apiKey) {
            $this->error('[DevToPublisher] Kein API-Key gesetzt.');
            return false;
        }

        $body = [
            'article' => [
                'title' => $article->title,
                'published' => true,
                'body_markdown' => $article->content,
                // Optional:
                // 'tags'       => ['php', 'wordpress'],
                // 'canonical_url' => 'https://example.com/original-post'
            ],
        ];

        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $apiKey,
            ],
            'body' => wp_json_encode($body),
            'timeout' => 10,
        ];

        $response = wp_remote_post(self::API_ENDPOINT, $args);

        if (is_wp_error($response)) {
            $this->error('[DevToPublisher] Fehler beim Senden: '.$response->get_error_message());
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 201) {
            $message = wp_remote_retrieve_body($response);
            $this->error("[DevToPublisher] Unerwartete Antwort ($code): $message");
            return false;
        }

        return true;
    }
}
