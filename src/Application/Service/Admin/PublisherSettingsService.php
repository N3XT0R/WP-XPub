<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Admin;

use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

final class PublisherSettingsService
{
    public function __construct(
        private PublisherRepositoryInterface $publisherRepo,
        private SettingsRepositoryInterface $settingsRepo
    ) {
    }

    public function getSettingsViewData(): array
    {
        $publishers = $this->publisherRepo->all();
        $active = $this->settingsRepo->get('xpub_publisher_targets', []);

        return [
            'publishers' => array_map(function ($publisher) use ($active) {
                return [
                    'slug' => $publisher->getSlug(),
                    'name' => $publisher->getName(),
                    'active' => in_array($publisher->getSlug(), $active, true),
                    'config' => array_map(fn($cfg) => [
                        'key' => $cfg->getKey(),
                        'value' => $cfg->getValue(),
                    ], $publisher->getConfigArray())
                ];
            }, $publishers)
        ];
    }
}