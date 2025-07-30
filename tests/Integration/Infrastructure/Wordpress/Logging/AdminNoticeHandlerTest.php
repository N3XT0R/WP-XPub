<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Logging;

use Monolog\Level;
use Monolog\LogRecord;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\AdminNoticeHandler;
use PHPUnit\Framework\TestCase;

class AdminNoticeHandlerTest extends TestCase
{
    public function testStoresNoticeWhenErrorLevel(): void
    {
        global $wp_options;
        $wp_options = [];

        $handler = new AdminNoticeHandler();
        $record = new LogRecord(new \DateTimeImmutable(), 'chan', Level::Error, 'msg');
        $handler->handle($record);

        $this->assertSame('msg', $wp_options['xpub_admin_notice'] ?? null);
    }
}
