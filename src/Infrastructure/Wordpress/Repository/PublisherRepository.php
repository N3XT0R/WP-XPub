<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Repository;

use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;
use wpdb;

class PublisherRepository implements PublisherRepositoryInterface
{
    public function all(): array
    {
        /** @var wpdb $wpdb */
        $wpdb = Database::get();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $rows = $wpdb->get_results("SELECT * FROM $publisherTable");

        if (!is_array($rows)) {
            return [];
        }

        $result = [];

        foreach ($rows as $row) {
            $configs = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $configTable WHERE publisher_id = %d", $row->id)
            );

            $configObjects = array_map(
                fn($c) => new PublisherConfig($c->config_key, maybe_unserialize($c->config_value)),
                $configs
            );

            $result[] = new Publisher($row->slug, $row->name, $configObjects);
        }

        return $result;
    }

    public function findBySlug(string $slug): ?Publisher
    {
        /** @var wpdb $wpdb */
        $wpdb = Database::get();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $publisherTable WHERE slug = %s", $slug)
        );

        if (!$row) {
            return null;
        }

        $configs = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $configTable WHERE publisher_id = %d", $row->id)
        );

        $configObjects = array_map(
            fn($c) => new PublisherConfig($c->config_key, maybe_unserialize($c->config_value)),
            $configs
        );

        return new Publisher($row->slug, $row->name, $configObjects);
    }

    public function updateConfig(string $slug, array $newConfig): void
    {
        /** @var wpdb $wpdb */
        $wpdb = Database::get();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $publisherId = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $publisherTable WHERE slug = %s", $slug)
        );

        if (!$publisherId) {
            return;
        }

        foreach ($newConfig as $key => $value) {
            $serializedValue = maybe_serialize($value);

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
                    ['config_value' => $serializedValue],
                    ['publisher_id' => $publisherId, 'config_key' => $key]
                );
            } else {
                $wpdb->insert(
                    $configTable,
                    [
                        'publisher_id' => $publisherId,
                        'config_key' => $key,
                        'config_value' => $serializedValue,
                    ]
                );
            }
        }
    }
}
