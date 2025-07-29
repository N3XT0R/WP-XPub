# OAuth Integration

This document explains how the OAuth flow is handled within the XPub plugin and how to use the available REST API
endpoints to initiate and complete the authorization process.

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

Provider config must be injected via WordPress filters:

```php
add_filter('wp_xpub_oauth_config', function () {
    return [
        'mastodon' => [
            'clientId' => '...',
            'clientSecret' => '...',
            'urlAuthorize' => 'https://your.instance/oauth/authorize',
            'urlAccessToken' => 'https://your.instance/oauth/token',
            'urlResourceOwnerDetails' => 'https://your.instance/api/v1/accounts/verify_credentials',
        ],
    ];
});
```

> Note: The `redirectUri` will be automatically set to the correct REST callback URL based on the current site.

---

## 🔒 State Protection

The OAuth `state` parameter is stored in WordPress via `update_option()` and validated on callback to prevent CSRF
attacks.

---

## 🔧 Adding a New Provider

1. Implement a new `OAuthTokenProviderInterface` class (extend `AbstractOAuthTokenProvider`)
2. Register it via the provider map filter:

```php
add_filter('wp_xpub_oauth_provider_map', function ($map) {
    $map['yourprovider'] = \Your\Namespace\YourOAuthTokenProvider::class;
    return $map;
});
```

3. Provide config via `wp_xpub_oauth_config`
4. Done! The REST endpoints now work automatically.

---

## 📁 Files Involved

- `OAuthTokenProviderFactory.php` – resolves the correct provider
- `OAuthController.php` – handles REST API logic
- `AbstractOAuthTokenProvider.php` – base class with token logic
- Provider classes (e.g. `MastodonOAuthTokenProvider.php`) – implement logic for each platform

---
