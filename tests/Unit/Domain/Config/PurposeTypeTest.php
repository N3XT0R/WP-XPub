<?php
namespace N3XT0R\XPub\Tests\Domain\Config;

use N3XT0R\XPub\Domain\Config\PurposeType;
use PHPUnit\Framework\TestCase;

class PurposeTypeTest extends TestCase
{
    public function testIsValid(): void
    {
        $this->assertTrue(PurposeType::isValid(PurposeType::DEFAULT));
        $this->assertTrue(PurposeType::isValid(PurposeType::OAUTH));
        $this->assertFalse(PurposeType::isValid('invalid'));
    }

    public function testAllReturnsAllTypes(): void
    {
        $this->assertSame([
            PurposeType::DEFAULT,
            PurposeType::OAUTH,
        ], PurposeType::all());
    }
}
