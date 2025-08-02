<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Adapter;

use DI\Container;
use N3XT0R\XPub\Application\Service\Queue\JobRunner;
use N3XT0R\XPub\Domain\Hook\FilterDispatcherInterface;
use N3XT0R\XPub\Infrastructure\Publishers\PublisherFactory;

final class WordpressCron
{
    public const CRON_HOOK = 'xpub_run_job_runner';
    public const CRON_SCHEDULE = 'xpub_every_minute';
    private const CRON_INTERVAL = 60;

    public const CRON_REFRESH_HOOK = 'xpub_refresh_tokens';

    private static ?Container $container = null;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    private static function container(): Container
    {
        if (self::$container === null) {
            throw new \RuntimeException('Container not initialized');
        }

        return self::$container;
    }

    public static function init(): void
    {
        self::container();
        add_filter('cron_schedules', [self::class, 'addSchedule']);
        add_action(self::CRON_HOOK, [self::class, 'run']);
        add_action(self::CRON_REFRESH_HOOK, [self::class, 'refreshTokens']);
    }

    public static function schedule(): void
    {
        if (!self::isRegistered()) {
            wp_schedule_event(time(), self::CRON_SCHEDULE, self::CRON_HOOK);
        }

        if (!self::isRefreshRegistered()) {
            wp_schedule_event(time(), 'xpub_every_ten_minutes', self::CRON_REFRESH_HOOK);;
        }
    }

    public static function isRegistered(): bool
    {
        return wp_next_scheduled(self::CRON_HOOK) !== false;
    }

    private static function isRefreshRegistered(): bool
    {
        return wp_next_scheduled(self::CRON_REFRESH_HOOK) !== false;
    }

    public static function addSchedule(array $schedules): array
    {
        $schedules[self::CRON_SCHEDULE] = [
            'interval' => self::CRON_INTERVAL,
            'display' => 'Every Minute',
        ];

        $schedules['xpub_every_ten_minutes'] = [
            'interval' => 600,
            'display' => 'Every 10 Minutes',
        ];

        return $schedules;
    }

    public static function unschedule(): void
    {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);
        if ($timestamp !== false) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
        }

        $refreshTimestamp = wp_next_scheduled(self::CRON_REFRESH_HOOK);
        if ($refreshTimestamp !== false) {
            wp_unschedule_event($refreshTimestamp, self::CRON_REFRESH_HOOK);
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

    public static function refreshTokens(): void
    {
        PublisherFactory::setFilterDispatcher(
            self::container()->get(FilterDispatcherInterface::class)
        );
        $jobRunner = self::container()->get(JobRunner::class);
        $jobRunner->refreshTokens();
    }


}
