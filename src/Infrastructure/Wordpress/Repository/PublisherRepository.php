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
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';

        $rows = $wpdb->get_results("SELECT * FROM $publisherTable");
        if (!is_array($rows)) {
            return [];
        }

        return array_map(
            fn($row) => new Publisher(
                $row->slug,
                $row->name,
                $this->fetchConfigObjects((int)$row->id)
            ),
            $rows
        );
    }

    public function findBySlug(string $slug, ?string $purposeType = null): ?Publisher
    {
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $publisherTable WHERE slug = %s", $slug)
        );

        if (!$row) {
            return null;
        }

        $configs = $this->fetchConfigObjects((int)$row->id, $purposeType);
        return new Publisher($row->slug, $row->name, $configs);
    }

    public function updateConfig(string $slug, array $newConfig): bool
    {
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $publisherId = (int)$wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $publisherTable WHERE slug = %s", $slug)
        );

        if (!$publisherId) {
            return false;
        }

        $success = true;
        foreach ($newConfig as $key => $item) {
            $success = $success && $this->upsertConfig($wpdb, $configTable, $publisherId, $key, $item);
        }

        return $success;
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

        $publisherId = (int)$wpdb->insert_id;

        foreach ($config as $key => $item) {
            $this->upsertConfig($wpdb, $configTable, $publisherId, $key, $item, false);
        }

        return true;
    }

    private function fetchConfigObjects(int $publisherId, ?string $purposeType = null): array
    {
        $wpdb = Database::get();
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $sql = "SELECT * FROM $configTable WHERE publisher_id = %d";
        $params = [$publisherId];

        if ($purposeType !== null) {
            $sql .= " AND purpose_type = %s";
            $params[] = $purposeType;
        }

        $rows = $wpdb->get_results($wpdb->prepare($sql, ...$params)) ?: [];

        return array_map([$this, 'mapRowToConfigObject'], $rows);
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

        $purpose = $this->determinePurposeType($wpdb, $table, $publisherId, $key, $item, $checkExisting);

        if ($checkExisting) {
            $exists = (int)$wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table WHERE publisher_id = %d AND config_key = %s",
                    $publisherId,
                    $key
                )
            );

            if ($exists > 0) {
                return (bool)$wpdb->update(
                    $table,
                    ['config_value' => $value, 'purpose_type' => $purpose],
                    ['publisher_id' => $publisherId, 'config_key' => $key]
                );
            }
        }

        return (bool)$wpdb->insert($table, [
            'publisher_id' => $publisherId,
            'config_key' => $key,
            'config_value' => $value,
            'purpose_type' => $purpose,
        ]);
    }

    private function determinePurposeType(
        wpdb $wpdb,
        string $table,
        int $publisherId,
        string $key,
        mixed $item,
        bool $checkExisting
    ): string {
        if (is_array($item) && array_key_exists('purpose_type', $item) && PurposeType::isValid($item['purpose_type'])) {
            return $item['purpose_type'];
        }

        if ($checkExisting) {
            $existingPurpose = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT purpose_type FROM $table WHERE publisher_id = %d AND config_key = %s",
                    $publisherId,
                    $key
                )
            );

            if ($existingPurpose !== null && PurposeType::isValid($existingPurpose)) {
                return $existingPurpose;
            }
        }

        return PurposeType::DEFAULT;
    }
}
