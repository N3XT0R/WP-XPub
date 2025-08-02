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

## ❗ Known WordPress Linter Limitations

This plugin is built following clean, hexagonal architecture.  
However, WordPress Plugin Review tools may flag false positives for:

- Use of dynamic translation methods
- Direct DB usage (when wrapped properly)
- Class-based modular design

This is intentional for maintainability and structure.

---
