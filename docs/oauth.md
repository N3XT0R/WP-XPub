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

1. Implement a new `OAuthTokenProviderInterface` (usually extend
   `AbstractOAuthTokenProvider`).
2. Register it via the provider map filter:

```php
add_filter('wp_xpub_oauth_provider_map', static function (array $map): array {
    $map['yourprovider'] = \Your\Namespace\YourOAuthTokenProvider::class;
    return $map;
});
```

3. Register the publisher slug and store its credentials via the XPUB settings
   screen (or with `PublisherSeederHelper::upsert()` as shown above).
4. Done! The REST endpoints now work automatically.

---

## ➡️ Using Providers in Custom Publishers

Within your own publisher you can request a token provider for a given slug.
This allows you to reuse the stored credentials and the automatic refresh logic.

```php
use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;

$provider = OAuthTokenProviderFactory::createFromPublisherSlug('mastodon');
$token = $provider->getAccessToken();
```

Pass the provider into your publisher class or use it directly when publishing.

---

## 📁 Files Involved

- `OAuthTokenProviderFactory.php` – resolves the correct provider
- `OAuthController.php` – handles REST API logic
- `AbstractOAuthTokenProvider.php` – base class with token logic
- Provider classes (e.g. `MastodonOAuthTokenProvider.php`) – implement logic for each platform

---
