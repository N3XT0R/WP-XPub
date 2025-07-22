<?php

namespace N3XT0R\XPub\Infrastructure\Logging\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class AdminNoticeHandler extends AbstractProcessingHandler
{
    private array $levelsToShow;

    public function __construct(
        array $levelsToShow = [Level::Error, Level::Warning],
        $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->levelsToShow = $levelsToShow;
    }

    protected function write(LogRecord $record): void
    {
        if (!is_admin()) {
            return;
        }

        if (in_array($record->level, $this->levelsToShow, true)) {
            update_option('xpub_admin_notice', $record->message);
        }
    }
}
