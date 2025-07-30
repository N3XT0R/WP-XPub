<?php

namespace N3XT0R\XPub\Tests\Domain\Config;

require_once __DIR__ . '/../../../../src/Domain/Config/oAuth/OAuthProviderConfigBuilder.php';

use N3XT0R\XPub\Domain\Config\OAuth\OAuthProviderConfigBuilder;
use PHPUnit\Framework\TestCase;

class OAuthProviderConfigBuilderTest extends TestCase
{
    public function testBuildAuthorizationCodeConfig(): void
    {
        $config = [
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'redirectUri' => 'http://example.com',
            'urlAuthorize' => 'http://auth',
            'urlAccessToken' => 'http://token',
            'urlResourceOwnerDetails' => 'http://details',
            'grant_type' => 'authorization_code',
            'scopes' => 'read write'
        ];
        $result = OAuthProviderConfigBuilder::build($config);
        $this->assertSame('id', $result['clientId']);
        $this->assertSame(['read','write'], $result['scopes']);
        $this->assertArrayHasKey('urlResourceOwnerDetails', $result);
    }

    public function testBuildClientCredentialsConfig(): void
    {
        $config = [
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'urlAuthorize' => 'http://auth',
            'urlAccessToken' => 'http://token',
            'urlResourceOwnerDetails' => 'http://details',
            'grant_type' => 'client_credentials'
        ];
        $result = OAuthProviderConfigBuilder::build($config);
        $this->assertArrayNotHasKey('redirectUri', $result);
        $this->assertSame('http://details', $result['urlResourceOwnerDetails']);
    }

    public function testBuildDefaultsAndOptionalFields(): void
    {
        $config = [
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'redirectUri' => 'http://example.com',
            'urlAuthorize' => 'http://auth',
            'urlAccessToken' => 'http://token',
            'scopes' => ['read', 'write'],
            'code' => 'abc'
        ];

        $result = OAuthProviderConfigBuilder::build($config);

        $this->assertSame(['read', 'write'], $result['scopes']);
        $this->assertSame('abc', $result['code']);
        $this->assertArrayNotHasKey('urlResourceOwnerDetails', $result);
    }

    public function testRequiredKeysForUnknownGrantTypeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        OAuthProviderConfigBuilder::requiredKeysForGrantType('invalid');
    }
}
