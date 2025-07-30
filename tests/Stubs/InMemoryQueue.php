<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Stubs;

use DateTimeImmutable;
use N3XT0R\XPub\Domain\Contracts\QueueRepositoryInterface;
use N3XT0R\XPub\Domain\Entity\Job;

final class InMemoryQueue implements QueueRepositoryInterface
{
    public array $jobs = [];
    public array $done = [];
    public array $failed = [];

    public function enqueue(Job $job): bool
    {
        $this->jobs[] = $job;
        return true;
    }

    public function getAllDueJobs(DateTimeImmutable $now): array
    {
        return $this->jobs;
    }

    public function markAsDone(Job $job): void
    {
        $this->done[] = $job;
    }

    public function markAsFailed(Job $job, string $errorMessage): void
    {
        $this->failed[] = [$job, $errorMessage];
    }
}
