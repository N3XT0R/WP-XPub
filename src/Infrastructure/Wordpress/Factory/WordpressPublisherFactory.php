<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;

final class WordpressPublisherFactory
{
    public static function create(): ArticlePublisher
    {
        $repository = new PublisherRepository();
        $settings = new WordpressSettingsRepository();
        $activeTargets = (new PublisherTargetProvider(
            $settings
        ))->getTargets();

        $instances = [];

        foreach ($repository->all() as $publisher) {
            if (in_array($publisher->getSlug(), $activeTargets, true)) {
                try {
                    $instances[] = PublisherFactory::createWithConfig(
                        $publisher->getSlug(),
                        $publisher->getConfigArray()
                    );
                } catch (\RuntimeException $e) {
                    LoggerFactory::create()->error($e->getMessage(), ['exception' => $e]);
                }
            }
        }

        return new ArticlePublisher($instances);
    }
}
