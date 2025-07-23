<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Repository;

use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Domain\Entity\PublisherConfig;
use N3XT0R\XPub\Domain\Repository\PublisherRepositoryInterface;
use N3XT0R\XPub\Infrastructure\Wordpress\Database\Database;

class PublisherRepository implements PublisherRepositoryInterface
{
    public function all(): array
    {
        /** @var \wpdb $wpdb */
        $wpdb = Database::get();

        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $rows = $wpdb->get_results("SELECT * FROM $publisherTable");

        // Safety check: if $rows is not an array, return empty
        if (!is_array($rows)) {
            return [];
        }

        $result = [];

        foreach ($rows as $row) {
            // Fetch config for each publisher
            $configs = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $configTable WHERE publisher_id = %d",
                    $row->id
                )
            );

            $configObjects = array_map(
                fn($c) => new PublisherConfig($c->config_key, $c->config_value),
                $configs
            );

            $result[] = new Publisher($row->slug, $row->name, $configObjects);
        }

        return $result;
    }

    /**
     * Optional: Hole einen einzelnen Publisher anhand des Slugs.
     */
    public function findBySlug(string $slug): ?Publisher
    {
        /** @var \wpdb $wpdb */
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
            fn($c) => new PublisherConfig($c->config_key, $c->config_value),
            $configs
        );

        return new Publisher($row->slug, $row->name, $configObjects);
    }
}
