<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;

final class SettingsSaveHandler
{
    public function handle(): void
    {
        (new SettingsSaveGuard())->ensureAuthorized();

        $activePublishers = $_POST['active_publishers'] ?? [];
        $configData = $_POST['config'] ?? [];

        $settingsRepo = new WordpressSettingsRepository();
        $settingsRepo->set('xpub_publisher_targets', $activePublishers);

        $this->savePublisherConfigs($configData);

        wp_redirect(admin_url('options-general.php?page=xpub-settings&updated=true'));
        exit;
    }

    private function savePublisherConfigs(array $configData): void
    {
        $repository = new PublisherRepository();

        foreach ($configData as $slug => $configs) {
            if (!is_array($configs)) {
                continue;
            }

            $repository->updateConfig($slug, $configs);
        }
    }
}
