<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Entity\Article;
use N3XT0R\XPub\Infrastructure\Markdown\HtmlToMarkdownRendererFactory;

class DevToPublisher extends PublisherAbstract
{
    private const API_ENDPOINT = 'https://dev.to/api/articles';

    protected function handlePublish(Article $article): bool
    {
        $apiKey = $this->getByKey('api_key');

        if (empty($apiKey)) {
            $this->error('Missing API key.');
            return false;
        }

        $renderer = HtmlToMarkdownRendererFactory::create();
        $markdown = $renderer->convert($article->htmlContent);
        $this->debug('Markdown: '.$markdown);

        $body = [
            'article' => [
                'title' => $article->title,
                'published' => true,
                'body_markdown' => $markdown,
                'tags' => $article->tags,
                // 'canonical_url' => $article->canonicalUrl ?? null,
            ],
        ];

        $response = wp_remote_post(self::API_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $apiKey,
            ],
            'body' => wp_json_encode($body),
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) {
            $this->error('Request failed: '.$response->get_error_message());
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 201) {
            $message = wp_remote_retrieve_body($response);
            $this->error("Unexpected response ($code): $message");
            return false;
        }

        $this->log('Article successfully published.');
        return true;
    }
}
