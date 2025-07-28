<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Logging;

use Monolog\Level;
use Monolog\LogRecord;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler\WPDBLogHandler;
use PHPUnit\Framework\TestCase;

class WPDBLogHandlerTest extends TestCase
{
    public function testInsertCalledWithCorrectData(): void
    {
        $wpdb = new class {
            public string $prefix = 'wp_';
            public array $lastInsert = [];
            public function insert($table, $data) { $this->lastInsert = [$table, $data]; }
        };

        $handler = new WPDBLogHandler($wpdb);
        $record = new LogRecord(new \DateTimeImmutable(), 'chan', Level::Info, 'hi');
        $handler->handle($record);

        $this->assertSame('wp_xpub_logs', $wpdb->lastInsert[0]);
        $this->assertSame('hi', $wpdb->lastInsert[1]['message']);
    }
}
