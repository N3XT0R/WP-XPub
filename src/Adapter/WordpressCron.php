<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use DI\Container;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Infrastructure\DI\ContainerProvider;
use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactory;

final class WordpressCron
{
    public const CRON_HOOK = 'xpub_run_job_runner';
    public const CRON_SCHEDULE = 'xpub_every_minute';
    private const CRON_INTERVAL = 60;

    private static ?Container $container = null;

    private static function container(): Container
    {
        if (self::$container === null) {
            self::$container = ContainerProvider::getContainer();
        }

        return self::$container;
    }

    public static function init(): void
    {
        self::container();
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
            'display' => 'Every Minute',
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
        PublisherFactory::setFilterDispatcher(
            self::container()->get(FilterDispatcherInterface::class)
        );
        $jobRunner = self::container()->get(JobRunner::class);
        $jobRunner->run();
    }
}
