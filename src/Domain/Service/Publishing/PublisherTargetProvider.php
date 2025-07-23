<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Application\Service\Publishing;

use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

final class PublisherTargetProvider
{
    public function __construct(private SettingsRepositoryInterface $settings)
    {
    }

    public function getTargets(): array
    {
        $targets = $this->settings->get('xpub_publisher_targets', []);
        return is_array($targets) ? $targets : [];
    }
}
