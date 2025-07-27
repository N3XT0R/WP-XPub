<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\ConfigurablePublisherInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Contracts\SlugAwareInterface;
use N3XT0R\XPub\Domain\Entity\Article;
use Psr\Log\LoggerInterface;

abstract class PublisherAbstract implements PublisherInterface, ConfigurablePublisherInterface, SlugAwareInterface
{
    protected array $config;
    protected ?LoggerInterface $logger;
    protected string $slug;

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getByKey(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    protected function log(string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->info($message, $context);
        }
    }

    protected function debug(string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->debug($message, $context);
        }
    }

    protected function error(string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->error($message, $context);
        }
    }

    public function publish(Article $article): bool
    {
        $article = $this->enrichArticleIfNeeded($article);

        // Pre-publish hook
        $article = apply_filters("xpub_pre_publish_{$this->slug}", $article, $this);

        // Actual publish logic
        $result = $this->handlePublish($article);

        // Post-publish hook
        do_action("xpub_post_publish_{$this->slug}", $article, $result, $this);

        return $result;
    }

    protected function enrichArticleIfNeeded(Article $article): Article
    {
        if (!empty($article->excerpt) || empty($article->content)) {
            return $article;
        }

        $excerpt = wp_strip_all_tags($article->content);
        $excerpt = trim($excerpt);

        if (mb_strlen($excerpt) > 280) {
            $excerpt = mb_substr($excerpt, 0, 277).'...';
        }

        return new Article(
            postId: $article->postId,
            title: $article->title,
            content: $article->content,
            excerpt: $excerpt,
            url: $article->url
        );
    }


    abstract protected function handlePublish(Article $article): bool;
}
