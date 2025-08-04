<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Admin\Validator\SettingsFormRequestValidator;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;

final class SettingsSaveHandler
{
    public function __construct(
        private SettingsFormRequestValidator $validator,
        private WordpressSettingsRepository $settingsRepo,
        private PublisherRepository $publisherRepo,
    ) {
    }

    public function handle(): void
    {
        $this->validator->validate();

        $activePublisherSlugs = isset($_POST['active_publishers']) ? wp_unslash($_POST['active_publishers']) : [];
        $activePublisherSlugs = array_map('sanitize_text_field', $activePublisherSlugs);

        $publisherConfigs = isset($_POST['config']) ? wp_unslash($_POST['config']) : [];
        $this->saveSettings($activePublisherSlugs, $publisherConfigs);
        
        wp_redirect(admin_url('options-general.php?page=xpub-settings&updated=true'));
        exit;
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
