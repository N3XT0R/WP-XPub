<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Contracts;

use Psr\Log\LoggerInterface;

interface ConfigurablePublisherInterface
{
    public function setLogger(?LoggerInterface $logger = null): void;

    public function getConfig(): array;

    public function setConfig(array $config): void;

    public function getByKey(string $key): mixed;
}