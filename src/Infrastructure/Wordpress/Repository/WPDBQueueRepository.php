<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Repository;

use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Job;
use wpdb;

class WPDBQueueRepository implements QueueRepositoryInterface
{
    public function __construct(private wpdb $db)
    {
    }

    public function enqueue(Job $job): bool
    {
        $existing = $this->findExistingJob($job);

        if (!$existing) {
            return $this->insertJob($job);
        }

        if ($this->shouldUpdate($existing, $job)) {
            return $this->updateJob((int)$existing['id'], $job);
        }

        // nothing changed
        return true;
    }

    public function findExistingJob(Job $job): ?array
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT id, payload, scheduled_at, status FROM {$this->db->prefix}xpub_queue 
             WHERE post_id = %d AND publisher = %s",
                $job->postId,
                $job->publisherKey
            ),
            ARRAY_A
        ) ?: null;
    }

    public function insertJob(Job $job): bool
    {
        $now = current_time('mysql', 1);

        $result = $this->db->insert("{$this->db->prefix}xpub_queue", [
            'post_id' => $job->postId,
            'publisher' => $job->publisherKey,
            'payload' => json_encode($job->payload),
            'scheduled_at' => $job->scheduledAt->format('Y-m-d H:i:s'),
            'attempts' => 0,
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $result !== false;
    }

    public function updateJob(int $id, Job $job): bool
    {
        $now = current_time('mysql', 1);

        $result = $this->db->update(
            "{$this->db->prefix}xpub_queue",
            [
                'payload' => json_encode($job->payload),
                'scheduled_at' => $job->scheduledAt->format('Y-m-d H:i:s'),
                'attempts' => 0,
                'status' => 'pending',
                'updated_at' => $now,
            ],
            ['id' => $id]
        );

        return $result !== false;
    }

    public function shouldUpdate(array $existing, Job $job): bool
    {
        return
            $existing['status'] !== 'pending' ||
            $existing['scheduled_at'] !== $job->scheduledAt->format('Y-m-d H:i:s') ||
            $existing['payload'] !== json_encode($job->payload);
    }

    /**
     * @param  \DateTimeImmutable  $now
     * @return array<Job>
     * @throws \Exception
     */
    public function getDueJobs(\DateTimeImmutable $now): array
    {
        $results = $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->db->prefix}xpub_queue WHERE status = %s AND scheduled_at <= %s",
                'pending',
                $now->format('Y-m-d H:i:s')
            ),
            ARRAY_A
        );

        return array_map(
            fn(array $row) => new Job(
                (int)$row['post_id'],
                $row['publisher'],
                json_decode($row['payload'], true),
                new \DateTimeImmutable($row['scheduled_at']),
                (int)$row['attempts'],
                $row['last_error'] ?? null,
                (int)$row['id']
            ),
            $results
        );
    }

    public function markAsDone(Job $job): void
    {
        $this->db->update(
            "{$this->db->prefix}xpub_queue",
            [
                'status' => 'done',
                'updated_at' => current_time('mysql', 1),
            ],
            ['id' => $job->id]
        );
    }

    public function markAsFailed(Job $job, string $errorMessage): void
    {
        $this->db->update(
            "{$this->db->prefix}xpub_queue",
            [
                'status' => 'failed',
                'attempts' => $job->attempts + 1,
                'last_error' => $errorMessage,
                'updated_at' => current_time('mysql', 1),
            ],
            ['id' => $job->id]
        );
    }
}
