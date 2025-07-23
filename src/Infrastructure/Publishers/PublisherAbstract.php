<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Publishers;

use N3XT0R\XPub\Domain\Contracts\ConfigurablePublisherInterface;
use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use Psr\Log\LoggerInterface;

abstract class PublisherAbstract implements PublisherInterface, ConfigurablePublisherInterface
{
    protected array $config;
    protected ?LoggerInterface $logger;

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
}
