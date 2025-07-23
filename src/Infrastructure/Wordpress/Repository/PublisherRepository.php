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
        $wpdb = Database::get();
        $publisherTable = $wpdb->prefix.'xpub_publishers';
        $configTable = $wpdb->prefix.'xpub_publisher_config';

        $rows = $wpdb->get_results("SELECT * FROM $publisherTable");

        $result = [];

        foreach ($rows as $row) {
            $configs = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $configTable WHERE publisher_id = %d",
                    $row->id
                )
            );

            $configObjects = array_map(fn($c) => new PublisherConfig($c->config_key, $c->config_value), $configs);

            $result[] = new Publisher($row->slug, $row->name, $configObjects);
        }

        return $result;
    }
}
