<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Logging;

use Monolog\Logger;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\AdminNoticeHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\WPDBLogHandler;

final class LoggerFactory
{
    public static function create(string $channel = 'xpub'): Logger
    {
        $logger = new Logger($channel);
        $database = Database::get();
        if ($database) {
            $logger->pushHandler(new WPDBLogHandler($database));
        }

        $logger->pushHandler(new AdminNoticeHandler());

        return $logger;
    }
}
