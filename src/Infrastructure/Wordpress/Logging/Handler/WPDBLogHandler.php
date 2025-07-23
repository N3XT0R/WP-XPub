<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Logging\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use wpdb;

class WPDBLogHandler extends AbstractProcessingHandler
{
    private wpdb $wpdb;
    private string $table;

    public function __construct(wpdb $wpdb, string $table = 'xpub_logs', $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix.$table;
    }

    protected function write(LogRecord $record): void
    {
        $this->wpdb->insert(
            $this->table,
            [
                'channel' => $record->channel,
                'level' => $record->level->value,
                'level_name' => $record->level->getName(),
                'message' => $record->message,
                'context' => wp_json_encode($record->context),
                'timestamp' => $record->datetime->format('Y-m-d H:i:s'),
            ]
        );
    }
}
