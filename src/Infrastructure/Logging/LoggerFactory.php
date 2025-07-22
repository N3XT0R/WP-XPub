<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use N3XT0R\XPub\Infrastructure\Database\Database;
use N3XT0R\XPub\Infrastructure\Logging\Handler\WPDBLogHandler;

class LoggerFactory
{
    public static function create(string $channel = 'xpub'): Logger
    {
        $logger = new Logger($channel);
        $dbHandler = new WPDBLogHandler(Database::get());
        $logger->pushHandler($dbHandler);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            $logFile = WP_CONTENT_DIR.'/uploads/xpub-logs/system.log';
            $handler = new StreamHandler($logFile, Level::Notice);
            $logger->pushHandler($handler);
        }

        return $logger;
    }
}