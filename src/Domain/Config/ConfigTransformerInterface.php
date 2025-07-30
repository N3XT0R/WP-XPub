<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config;

interface ConfigTransformerInterface
{
    public function supports(array $config): bool;

    public function transform(array $config): array;
}