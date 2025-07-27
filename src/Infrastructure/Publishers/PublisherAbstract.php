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
        $this->setConfig($config);
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

    protected function error(string $message, array $context = []): void
    {
        if ($this->logger !== null) {
            $this->logger->error($message, $context);
        }
    }

    public function publish(Article $article): bool
    {
        // Pre-publish hook
        $article = apply_filters("xpub_pre_publish_{$this->slug}", $article, $this);

        // Actual publish logic
        $result = $this->handlePublish($article);

        // Post-publish hook
        do_action("xpub_post_publish_{$this->slug}", $article, $result, $this);

        return $result;
    }

    abstract protected function handlePublish(Article $article): bool;
}
