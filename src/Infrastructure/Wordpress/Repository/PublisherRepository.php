<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Repository;

use N3XT0R\XPub\Domain\Config\PurposeType;
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

            $configObjects = array_map([$this, 'mapRowToConfigObject'], $configs);

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

        $configObjects = array_map([$this, 'mapRowToConfigObject'], $configs);

        return new Publisher($row->slug, $row->name, $configObjects);
    }

    public function updateConfig(string $slug, array $newConfig): bool
    {
        /** @var wpdb $wpdb */
        $wpdb = Database::get();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $publisherId = (int)$wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $publisherTable WHERE slug = %s", $slug)
        );

        if (!$publisherId) {
            return false;
        }

        $result = true;

        foreach ($newConfig as $key => $item) {
            $success = $this->upsertConfig($wpdb, $configTable, $publisherId, $key, $item);
            $result = $result && $success;
        }

        return $result;
    }

    public function create(string $slug, string $name, array $config): bool
    {
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $inserted = $wpdb->insert($publisherTable, [
            'slug' => $slug,
            'name' => $name,
        ]);

        if (!$inserted) {
            return false;
        }

        $publisherId = $wpdb->insert_id;

        foreach ($config as $key => $item) {
            $this->upsertConfig($wpdb, $configTable, $publisherId, $key, $item, false);
        }

        return true;
    }

    private function mapRowToConfigObject(object $row): PublisherConfig
    {
        return new PublisherConfig(
            $row->config_key,
            maybe_unserialize($row->config_value),
            $row->purpose_type ?? PurposeType::DEFAULT
        );
    }

    private function upsertConfig(
        wpdb $wpdb,
        string $table,
        int $publisherId,
        string $key,
        mixed $item,
        bool $checkExisting = true
    ): bool {
        $value = is_array($item) && array_key_exists('value', $item)
            ? maybe_serialize($item['value'])
            : maybe_serialize($item);

        $purpose = is_array($item) && array_key_exists('purpose_type', $item)
            ? $item['purpose_type']
            : PurposeType::DEFAULT;

        if (!PurposeType::isValid($purpose)) {
            $purpose = PurposeType::DEFAULT;
        }

        if ($checkExisting) {
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table WHERE publisher_id = %d AND config_key = %s",
                    $publisherId,
                    $key
                )
            );

            if ((int)$exists > 0) {
                return (bool)$wpdb->update(
                    $table,
                    ['config_value' => $value, 'purpose_type' => $purpose],
                    ['publisher_id' => $publisherId, 'config_key' => $key]
                );
            }
        }

        return (bool)$wpdb->insert(
            $table,
            [
                'publisher_id' => $publisherId,
                'config_key' => $key,
                'config_value' => $value,
                'purpose_type' => $purpose,
            ]
        );
    }
}
