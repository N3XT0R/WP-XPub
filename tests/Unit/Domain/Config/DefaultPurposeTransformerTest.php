<?php

namespace N3XT0R\XPub\Tests\Domain\Config;

use N3XT0R\XPub\Domain\Config\DefaultPurposeTransformer;
use N3XT0R\XPub\Domain\Config\PurposeType;
use PHPUnit\Framework\TestCase;

class DefaultPurposeTransformerTest extends TestCase
{
    public function testSupportsReturnsTrueWhenNoGrantType(): void
    {
        $transformer = new DefaultPurposeTransformer();
        $this->assertTrue($transformer->supports(['foo' => 'bar']));
    }

    public function testTransformAddsPurposeType(): void
    {
        $transformer = new DefaultPurposeTransformer();
        $result = $transformer->transform(['key' => 'value']);
        $this->assertSame(['key' => ['value' => 'value', 'purpose_type' => PurposeType::DEFAULT]], $result);
    }
}
