<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Domain\Entity;

class Job
{
    public function __construct(
        public readonly int $postId,
        public readonly string $publisherKey,
        public readonly array $payload,
        public readonly \DateTimeImmutable $scheduledAt,
        public readonly int $attempts = 0,
        public readonly ?string $lastError = null,
        public readonly ?int $id = null,
    ) {
    }

}