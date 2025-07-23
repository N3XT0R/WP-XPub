<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Settings;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

class WordpressSettingsRepository implements SettingsRepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return get_option($key, $default);
    }

    public function set(string $key, mixed $value): bool
    {
        return update_option($key, $value);
    }

    public function delete(string $key): bool
    {
        return delete_option($key);
    }
}
