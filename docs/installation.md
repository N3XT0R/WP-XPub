# Installation Guide: WP-XPub

WP-XPub is a multi-channel content publishing plugin designed for clean architecture and developer-friendliness. Below
are the supported installation methods.

---

## 📦 Composer Installation (Preferred for Developers)

If your WordPress project is managed via Composer (e.g., Bedrock), you can install WP-XPub like any other package:

```bash
composer require n3xt0r/wp-xpub
```

If the package is not on Packagist, add the GitHub repository manually:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/N3XT0R/WP-XPub"
    }
  ]
}
```

Then run:

```bash
composer require n3xt0r/wp-xpub
```

> Make sure to autoload the plugin or register it via Bedrock's `mu-plugins` or `plugins/` folder.

---

## 📁 Manual Installation

1. Download the latest release from GitHub:  
   [https://github.com/N3XT0R/WP-XPub](https://github.com/N3XT0R/WP-XPub)

2. Extract the archive to your WordPress plugin directory:

   ```
   wp-content/plugins/xpub-multi-channel-publisher/
   ```

3. Activate the plugin via WordPress Admin > Plugins.
4. WP-XPub registers cron tasks automatically (one for the job queue and one for
   token refresh). Ensure WP Cron runs on your site or configure a real cron job
   to trigger them.

---

## ⚙️ Requirements

- PHP 8.2+
- WordPress 6.0+ (tested with 6.8.2)
- Composer (for development mode)

---

## ✅ Optional: Developer Mode

Clone the repository and install dependencies:

```bash
git clone https://github.com/N3XT0R/WP-XPub.git
cd WP-XPub
composer install
```

You may then symlink it into your WordPress `plugins/` directory or load via Bedrock's `mu-plugins`.

---

## 🔐 OAuth Publisher Configuration

WP-XPub supports OAuth2-based publishing to platforms like **LinkedIn**, **Mastodon**, and others.  
Each publisher is modular and can be configured independently via a migration or admin UI.

To enable an OAuth-based publisher:

1. Make sure the corresponding migration exists (e.g. `Migration_5` for LinkedIn)
2. Ensure proper `clientId`, `clientSecret`, and redirect URIs are configured
3. Use `OAuthPublisherSeederHelper::upsert()` to register the service
4. Authenticate the publisher via the WordPress backend once credentials are set

OAuth tokens are stored securely via your configured `SettingsRepository`.

> 📄 See [`setup/linkedin.md`](setup/linkedin.md) for a full example on how to configure the LinkedIn Publisher.

### Available Publishers (OAuth)

| Publisher Slug | Platform | Grant Type           | Scopes Example                           | Setup Link                             |
|----------------|----------|----------------------|------------------------------------------|----------------------------------------|
| `linkedin`     | LinkedIn | `authorization_code` | `w_member_social`, `r_liteprofile`, etc. | [setup/linkedin.md](setup/linkedin.md) |
| `mastodon`     | Mastodon | `authorization_code` | `write:statuses`                         | [setup/mastodon.md](setup/mastodon.md) |

More platforms can be added by extending `PublisherAbstract` and using `SupportsOAuthFactoryTrait`.

---

## ❗ Known WordPress Linter Limitations

This plugin is built following clean, hexagonal architecture.  
However, WordPress Plugin Review tools may flag false positives for:

- Use of dynamic translation methods
- Direct DB usage (when wrapped properly)
- Class-based modular design

This is intentional for maintainability and structure.

---
