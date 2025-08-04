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
                    'config' => $publisher->getCategorizedConfigArray(),
                ];
            }, $publishers),
            'activePublisherSlugs' => $active
        ];
    }

    public function saveSettings(array $activePublisherSlugs, array $publisherConfigs): void
    {
        $this->settingsRepo->set('xpub_publisher_targets', $activePublisherSlugs);

        foreach ($publisherConfigs as $slug => $configs) {
            if (!is_array($configs) || $slug === '') {
                continue;
            }

            $this->publisherRepo->updateConfig($slug, $configs);
        }
    }
}
