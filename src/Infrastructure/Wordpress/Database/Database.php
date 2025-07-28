<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Database;

use wpdb;

final class Database
{
    private static ?wpdb $instance = null;

    public static function get(): ?wpdb
    {
        if (!self::$instance) {
            global $wpdb;
            self::$instance = $wpdb;
        }

        return self::$instance;
    }
}
