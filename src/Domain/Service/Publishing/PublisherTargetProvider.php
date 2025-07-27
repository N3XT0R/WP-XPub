<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Service\Publishing;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

final class PublisherTargetProvider
{
    protected SettingsRepositoryInterface $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function getTargets(): array
    {
        $targets = $this->settings->get('xpub_publisher_targets', []);
        return is_array($targets) ? $targets : [];
    }
}
