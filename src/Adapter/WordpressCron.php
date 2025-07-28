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
use Psr\Log\NullLogger;

final class WordpressCron
{
    public static function register(): void
    {
        if (!wp_next_scheduled('xpub_run_job_runner')) {
            wp_schedule_event(time(), 'xpub_every_five_minutes', 'xpub_run_job_runner');
        }

        add_filter('cron_schedules', function (array $schedules): array {
            $schedules['xpub_every_five_minutes'] = [
                'interval' => 300,
                'display' => 'Every 5 Minutes',
            ];
            return $schedules;
        });
    }

    public static function run(): void
    {
        $jobRunner = new JobRunner(
            queue: new WPDBQueueRepository(Database::get()),
            publisherSelector: new PublisherSelector(
                new PublisherRepository(),
                new PublisherFactory(),
                new NullLogger()
            ),
            articleFactory: new ArticleFactory(new WpPostContentRenderer()),
            logger: LoggerFactory::create(),
        );

        $jobRunner->run();
    }
}
