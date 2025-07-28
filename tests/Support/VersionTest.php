<?php

namespace N3XT0R\XPub\Tests\Support;

use PHPUnit\Framework\TestCase;
use N3XT0R\XPub\Support\Version;

class VersionTest extends TestCase
{
    public function testReturnsDefinedConstant(): void
    {
        define('XPUB_VERSION', '1.2.3');
        $this->assertSame('1.2.3', Version::get());
    }
}
