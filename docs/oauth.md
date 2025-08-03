# OAuth Integration

This document explains how WP-XPub handles OAuth and how you can extend the
implementation or consume the flow in your own publishers.

---

## Overview

The OAuth flow is designed to allow external providers (e.g. Mastodon) to be connected securely via token-based
authentication. The implementation is provider-agnostic and supports dynamic registration through a factory-based
architecture.

---

## 📘 Endpoints

### 1. Start Authorization

**URL:**

```
GET /wp-json/xpub/v1/oauth/{provider}/start
```

**Description:**

Redirects the user to the external OAuth provider’s authorization screen.

**Parameters:**

| Name     | Type   | Required | Description                         |
|----------|--------|----------|-------------------------------------|
| provider | string | yes      | The provider slug (e.g. `mastodon`) |

**Response:**

```json
{
  "url": "https://oauth.provider.com/authorize?client_id=..."
}
```

---

### 2. Handle Callback

**URL:**

```
GET /wp-json/xpub/v1/oauth/{provider}/callback
```

**Description:**

Handles the OAuth callback from the provider after the user authorized the app.

**Query Parameters:**

| Name  | Type   | Required | Description                          |
|-------|--------|----------|--------------------------------------|
| code  | string | yes      | The authorization code from provider |
| state | string | yes      | The OAuth state parameter            |

**Response:**

```json
{
  "success": true
}
```

If state validation fails or an error occurs during token exchange, a `403` or `500` error is returned.

---

### 3. Request Client Credentials Token

**URL:**

```
POST /wp-json/xpub/v1/oauth/{provider}/client-token
```

**Description:**

Retrieves an access token using the client credentials grant. This is useful for server-to-server communication or
automated publishing flows.

**Body Parameters:**

None.

**Response:**

```json
{
  "access_token": "abcdef...",
  "expires": 1729999999,
  "token_type": "Bearer",
  "success": true
}
```

If an error occurs, a `500` response is returned with details.

---

### Endpoint: Token Status

**URL:**

```
GET /wp-json/xpub/v1/oauth/{provider}/status
```

**Description:**
Checks whether a token for the given provider is currently stored and valid.

**Response:**

```json
{
  "connected": true
}
```

or

```json
{
  "connected": false
}
```

---

## 🧠 Internals

### Factory-based Provider Resolution

All token providers are instantiated via the `OAuthTokenProviderFactory`. The factory:

- Resolves the provider class based on the slug
- Instantiates the correct class (e.g. `MastodonOAuthTokenProvider`)
- Injects settings and dependencies
- Fetches the correct `Publisher` configuration

Example resolution flow:

```
slug => publisher config => token provider class => OAuth provider instance
```

### Token Storage

Tokens are stored using a `SettingsRepositoryInterface` implementation (e.g. WordPress options API). Each provider uses
its own storage key.

Example stored values:

```php
[
  'access_token' => '...',
  'refresh_token' => '...',
  'expires' => 1729999999
]
```

### Refresh Flow

If a token is expired, it will be automatically refreshed via the `refresh_token` grant — assuming a valid refresh token
is stored.

---

## 🛠 Configuration

Provider credentials are stored in the publisher configuration. You can enter
them via **Settings → XPUB** or seed them programmatically:

```php
use N3XT0R\XPub\Support\DefaultPublisherSeederHelper;

DefaultPublisherSeederHelper::upsert('mastodon', 'Mastodon', [
    'grant_type' => 'authorization_code', //or client_credentials
    'clientId' => '',
    'clientSecret' => '',
    'urlAuthorize' => 'https://your.instance/oauth/authorize',
    'urlAccessToken' => 'https://your.instance/oauth/token',
    'urlResourceOwnerDetails' => 'https://your.instance/api/v1/accounts/verify_credentials',
]);
```

The `redirectUri` is automatically set to the correct REST callback URL for the
current site.

---

## 🔒 State Protection

The OAuth `state` parameter is stored in WordPress via `update_option()` and validated on callback to prevent CSRF
attacks.

---

## 🔧 Adding a New Provider

### 1. Create a Provider Class

```php
use N3XT0R\XPub\Infrastructure\OAuth\AbstractOAuthTokenProvider;

<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\OAuth\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;

class MyCustomOAuthProvider extends AbstractOAuthTokenProvider
{
    public function __construct(
        GenericProvider $provider,
        SettingsRepositoryInterface $settings,
        string $grantType = 'authorization_code',
    ) {
        parent::__construct($provider, $settings, 'MyCustomOAuth', $grantType);
    }
}

```

### 2. Register the Provider

```php
add_filter('wp_xpub_oauth_provider_map', static function (array $map): array {
    $map['mycustom'] = \My\Namespace\MyCustomOAuthProvider::class;
    return $map;
});
```

### 3. Seed the Publisher

```php
DefaultPublisherSeederHelper::upsert('mycustom', 'My Custom Provider', [
    'grant_type' => 'authorization_code',
    'clientId' => '...',
    'clientSecret' => '...',
    'urlAuthorize' => 'https://example.com/oauth/authorize',
    'urlAccessToken' => 'https://example.com/oauth/token',
    'urlResourceOwnerDetails' => 'https://example.com/api/me',
]);
```

---

## ➡️ Using Providers in Custom Publishers

Within your publisher:

```php
class MyPublisher extends PublisherAbstract implements SupportsOAuthFactoryInterface
{
    use SupportsOAuthFactoryTrait;

    protected function handlePublish(Article $article): bool
    {
        $provider = $this->getOAuthTokenProviderFactory()->createFromPublisherSlug('mycustom');
        $token = $provider->getAccessToken();
        return true;
    }
}
```

---

## 📁 Files Involved

- `OAuthTokenProviderFactory.php`
- `OAuthController.php`
- `AbstractOAuthTokenProvider.php`
- Custom provider classes (e.g. `MyCustomOAuthProvider.php`)
