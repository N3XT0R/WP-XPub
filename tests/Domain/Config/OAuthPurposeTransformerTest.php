<?php

namespace N3XT0R\XPub\Tests\Domain\Config;

use N3XT0R\XPub\Domain\Config\oAuth\OAuthPurposeTransformer;
use N3XT0R\XPub\Domain\Config\PurposeType;
use PHPUnit\Framework\TestCase;

class OAuthPurposeTransformerTest extends TestCase
{
    public function testSupportsWhenGrantTypePresent(): void
    {
        $transformer = new OAuthPurposeTransformer();
        $this->assertTrue($transformer->supports(['grant_type' => 'foo']));
    }

    public function testTransformAddsOAuthPurpose(): void
    {
        $transformer = new OAuthPurposeTransformer();
        $result = $transformer->transform(['key' => 'value']);
        $this->assertSame(['key' => ['value' => 'value', 'purpose_type' => PurposeType::OAUTH]], $result);
    }
}
