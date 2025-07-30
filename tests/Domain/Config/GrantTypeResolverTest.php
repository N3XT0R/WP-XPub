<?php

namespace N3XT0R\XPub\Tests\Domain\Config;

use N3XT0R\XPub\Infrastructure\OAuth\Support\GrantTypeResolver;
use PHPUnit\Framework\TestCase;

class GrantTypeResolverTest extends TestCase
{
    public function testResolveWithExplicitGrantType(): void
    {
        $config = ['grantType' => GrantTypeResolver::CLIENT_CREDENTIALS];
        $this->assertSame(GrantTypeResolver::CLIENT_CREDENTIALS, GrantTypeResolver::resolve($config));
    }

    public function testResolveWithMissingRedirectUriDefaultsToClientCredentials(): void
    {
        $config = [
            'urlAuthorize' => 'http://example.com',
            'redirectUri' => '',
        ];
        $this->assertSame(GrantTypeResolver::CLIENT_CREDENTIALS, GrantTypeResolver::resolve($config));
    }

    public function testResolveFallbackToAuthorizationCode(): void
    {
        $config = [
            'redirectUri' => 'http://example.com',
            'urlAuthorize' => 'http://example.com',
        ];
        $this->assertSame(GrantTypeResolver::AUTHORIZATION_CODE, GrantTypeResolver::resolve($config));
    }

    public function testIsValid(): void
    {
        $this->assertTrue(GrantTypeResolver::isValid(GrantTypeResolver::CLIENT_CREDENTIALS));
        $this->assertFalse(GrantTypeResolver::isValid('invalid'));
    }
}
