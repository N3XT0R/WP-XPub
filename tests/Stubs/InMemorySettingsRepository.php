<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Stubs;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

final class InMemorySettingsRepository implements SettingsRepositoryInterface
{
    /**
     * @param array<string,mixed> $settings
     */
    public function __construct(private array $settings = []) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value): bool
    {
        $this->settings[$key] = $value;
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->settings[$key]);
        return true;
    }
}
