<?php

namespace N3XT0R\XPub\Tests\Domain\Config;

use N3XT0R\XPub\Domain\Config\oAuth\OAuthGrantValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OAuthGrantValidatorTest extends TestCase
{
    public function testValidatePassesWithRequiredKeys(): void
    {
        $validator = new OAuthGrantValidator();
        $config = [
            'grant_type' => 'client_credentials',
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'urlAccessToken' => 'token',
            'urlResourceOwnerDetails' => 'details',
            'urlAuthorize' => 'auth',
        ];
        $validator->validate($config);
        $this->assertTrue(true); // no exception
    }

    public function testValidateThrowsOnMissingKeys(): void
    {
        $validator = new OAuthGrantValidator();
        $config = ['grant_type' => 'client_credentials'];
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($config);
    }

    public function testUnsupportedGrantTypeThrows(): void
    {
        $validator = new OAuthGrantValidator();
        $config = ['grant_type' => 'unsupported'];
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($config);
    }
}
