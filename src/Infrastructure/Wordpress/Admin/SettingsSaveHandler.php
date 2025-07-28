<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator\SettingsFormRequestValidator;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;

final class SettingsSaveHandler
{
    public function handle(): void
    {
        (new SettingsFormRequestValidator())->validate();

        $activePublisherSlugs = isset($_POST['active_publishers']) ? wp_unslash($_POST['active_publishers']) : [];
        $activePublisherSlugs = array_map('sanitize_text_field', $activePublisherSlugs);

        $publisherConfigs = isset($_POST['config']) ? wp_unslash($_POST['config']) : [];

        $settingsRepo = new WordpressSettingsRepository();
        $settingsRepo->set('xpub_publisher_targets', $activePublisherSlugs);

        $this->persistPublisherConfigs($publisherConfigs);

        wp_redirect(admin_url('options-general.php?page=xpub-settings&updated=true'));
        exit;
    }


    private function persistPublisherConfigs(array $publisherConfigs): void
    {
        $repository = new PublisherRepository();

        foreach ($publisherConfigs as $slug => $configs) {
            if (!is_array($configs) || empty($slug)) {
                continue;
            }

            $repository->updateConfig($slug, $configs);
        }
    }
}
