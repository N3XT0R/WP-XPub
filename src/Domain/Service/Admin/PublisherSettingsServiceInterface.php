<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Service\Admin;

interface PublisherSettingsServiceInterface
{
    public function getSettingsViewData(): array;

    public function saveSettings(array $activePublisherSlugs, array $publisherConfigs): void;
}
