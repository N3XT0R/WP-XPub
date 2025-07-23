<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Logging;

use Monolog\Logger;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\AdminNoticeHandler;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\WPDBLogHandler;

class LoggerFactory
{
    public static function create(string $channel = 'xpub'): Logger
    {
        $logger = new Logger($channel);
        $dbHandler = new WPDBLogHandler(Database::get());
        $logger->pushHandler($dbHandler);
        $logger->pushHandler(new AdminNoticeHandler());

        return $logger;
    }
}