<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Settings;

interface SettingsRepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): bool;

    public function delete(string $key): bool;
}
