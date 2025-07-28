<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Factory;

use N3XT0R\XPub\Application\Factory\PublisherFactory;
use N3XT0R\XPub\Domain\Service\ArticlePublisher;
use N3XT0R\XPub\Domain\Service\Publishing\PublisherTargetProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Logging\LoggerFactory;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use Psr\Log\LoggerInterface;

final readonly class WordpressPublisherFactory
{
    protected LoggerInterface $loggerInterface;

    public function __construct(
        private PublisherRepository $repository,
        private WordpressSettingsRepository $settings,
        ?LoggerInterface $loggerInterface = null
    ) {
        $this->loggerInterface = $loggerInterface ?? LoggerFactory::create();
    }

    public function create(): ArticlePublisher
    {
        $activeTargets = (new PublisherTargetProvider($this->settings))->getTargets();
        $instances = [];

        foreach ($this->repository->all() as $publisher) {
            if (in_array($publisher->getSlug(), $activeTargets, true)) {
                try {
                    $instances[] = PublisherFactory::createWithConfig(
                        $publisher->getSlug(),
                        $publisher->getConfigArray()
                    );
                } catch (\RuntimeException $e) {
                    $this->loggerInterface->error($e->getMessage(), ['exception' => $e]);
                }
            }
        }

        return new ArticlePublisher($instances);
    }
}
