<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Config;

interface ConfigValidatorInterface
{
    public function supports(array $config): bool;

    public function validate(array $config): void;
}