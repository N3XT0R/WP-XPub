<?php

namespace N3XT0R\XPub\Adapter;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Application\Publisher\PublisherSelector;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Infrastructure\Wordpress\Content\WpPostContentRenderer;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Factory\ArticleFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\WPDBQueueRepository;

final class WordpressCron
{
    public const CRON_HOOK = 'xpub_run_job_runner';
    public const CRON_SCHEDULE = 'xpub_every_minute';
    private const CRON_INTERVAL = 60;

    public static function init(): void
    {
        add_filter('cron_schedules', [self::class, 'addSchedule']);
        add_action(self::CRON_HOOK, [self::class, 'run']);
    }

    public static function schedule(): void
    {
        if (!self::isRegistered()) {
            wp_schedule_event(time(), self::CRON_SCHEDULE, self::CRON_HOOK);
        }
    }

    public static function isRegistered(): bool
    {
        return wp_next_scheduled(self::CRON_HOOK) !== false;
    }

    public static function addSchedule(array $schedules): array
    {
        $schedules[self::CRON_SCHEDULE] = [
            'interval' => self::CRON_INTERVAL,
            'display' => 'Every 5 Minutes',
        ];
        return $schedules;
    }

    public static function unschedule(): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp !== false) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }
    }


    public static function run(): void
    {
        $jobRunner = new JobRunner(
            queue: new WPDBQueueRepository(Database::get()),
            publisherSelector: new PublisherSelector(
                new PublisherRepository(),
                new PublisherFactory(),
                LoggerFactory::create()
            ),
            articleFactory: new ArticleFactory(new WpPostContentRenderer()),
            logger: LoggerFactory::create(),
        );

        $jobRunner->run();
    }
}
