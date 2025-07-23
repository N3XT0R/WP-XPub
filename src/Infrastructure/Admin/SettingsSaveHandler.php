<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Admin;

use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use wpdb;

final class SettingsSaveHandler
{
    public function handle(): void
    {
        if (
            !current_user_can('manage_options') ||
            !isset($_POST['_wpnonce']) ||
            !wp_verify_nonce($_POST['_wpnonce'], 'xpub_save_settings')
        ) {
            wp_die('Berechtigung verweigert oder ungültige Anfrage.');
        }

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
        /** @var wpdb $wpdb */
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        foreach ($configData as $slug => $configs) {
            $publisherId = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM $publisherTable WHERE slug = %s", $slug)
            );

            if (!$publisherId) {
                continue;
            }

            foreach ($configs as $key => $value) {
                $exists = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM $configTable WHERE publisher_id = %d AND config_key = %s",
                        $publisherId,
                        $key
                    )
                );

                if ((int)$exists > 0) {
                    $wpdb->update(
                        $configTable,
                        ['config_value' => maybe_serialize($value)],
                        ['publisher_id' => $publisherId, 'config_key' => $key]
                    );
                } else {
                    $wpdb->insert(
                        $configTable,
                        [
                            'publisher_id' => $publisherId,
                            'config_key' => $key,
                            'config_value' => maybe_serialize($value),
                        ]
                    );
                }
            }
        }
    }
}
