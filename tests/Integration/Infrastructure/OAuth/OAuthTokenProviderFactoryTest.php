<?php

namespace N3XT0R\XPub\Tests\Infrastructure\OAuth;

use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use N3XT0R\XPub\Domain\Config\PurposeType;
use N3XT0R\XPub\Domain\Entity\Publisher;
use N3XT0R\XPub\Infrastructure\OAuth\Provider\MastodonOAuthTokenProvider;
use N3XT0R\XPub\Infrastructure\Wordpress\Repository\PublisherRepository;
use N3XT0R\XPub\Infrastructure\Wordpress\Settings\WordpressSettingsRepository;
use PHPUnit\Framework\TestCase;

class OAuthTokenProviderFactoryTest extends TestCase
{
    public function testCreateFromPublisherSlugReturnsProvider(): void
    {
        $publisher = new Publisher('mastodon', 'Mastodon', [
            new \N3XT0R\XPub\Domain\Entity\PublisherConfig('clientId', 'id'),
            new \N3XT0R\XPub\Domain\Entity\PublisherConfig('clientSecret', 'secret'),
            new \N3XT0R\XPub\Domain\Entity\PublisherConfig('urlAuthorize', 'a'),
            new \N3XT0R\XPub\Domain\Entity\PublisherConfig('urlAccessToken', 't'),
            new \N3XT0R\XPub\Domain\Entity\PublisherConfig('urlResourceOwnerDetails', 'd'),
        ]);

        $repository = $this->createMock(PublisherRepository::class);
        $repository->method('findBySlug')->with('mastodon', PurposeType::OAUTH)->willReturn($publisher);
        $settings = $this->createMock(WordpressSettingsRepository::class);

        $factory = new OAuthTokenProviderFactory($repository, $settings);
        $provider = $factory->createFromPublisherSlug('mastodon');
        $this->assertInstanceOf(MastodonOAuthTokenProvider::class, $provider);
    }

    public function testCreateThrowsWhenProviderMissing(): void
    {
        $repository = $this->createMock(PublisherRepository::class);
        $repository->method('findBySlug')->willReturn(null);
        $settings = $this->createMock(WordpressSettingsRepository::class);

        $factory = new OAuthTokenProviderFactory($repository, $settings);
        $this->expectException(\RuntimeException::class);
        $factory->createFromPublisherSlug('unknown');
    }
}
