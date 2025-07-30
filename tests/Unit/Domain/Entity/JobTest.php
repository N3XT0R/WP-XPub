<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Domain\Entity;

use N3XT0R\XPub\Domain\Entity\Job;
use PHPUnit\Framework\TestCase;

final class JobTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $scheduled = new \DateTimeImmutable('2024-05-01 12:00:00');
        $job = new Job(
            5,
            'devto',
            ['foo' => 'bar'],
            $scheduled,
            2,
            'err',
            10
        );

        $this->assertSame(5, $job->postId);
        $this->assertSame('devto', $job->publisherKey);
        $this->assertSame(['foo' => 'bar'], $job->payload);
        $this->assertSame($scheduled, $job->scheduledAt);
        $this->assertSame(2, $job->attempts);
        $this->assertSame('err', $job->lastError);
        $this->assertSame(10, $job->id);
    }
}
