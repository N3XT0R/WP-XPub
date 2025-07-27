# WP-XPub

**Flexible Multi-Channel Auto Publisher for WordPress**

WP-XPub is a lightweight, extensible auto-publishing plugin for WordPress. It allows you to publish your posts to
multiple external platforms – including LinkedIn, Mastodon, Dev.to, and more – either immediately or via scheduled jobs.

---

## 🚀 Features

- 🔁 Auto-publish WordPress posts to external platforms
- 🧩 Modular client driver system (easily add new platforms)
- 🧵 Pre- and post-publish hooks for full customization
- 📦 Composer-ready (PSR-4 autoloading, modern architecture)
- 🐘 Built with PHP – no external API calls required, uses Monolog for local logging

---

## 📦 Installation

```bash
composer require n3xt0r/wp-xpub
```

Activate the plugin in WordPress and configure publishers via  
**Settings > XPUB**

---

## ✅ Requirements

- PHP 8.2+
- WordPress 6.8.2+
- Composer (optional for development)

---

## 📚 Documentation

The full documentation is now available in the `docs/` folder:

- [Overview & Index](docs/index.md)
- [Creating Custom Publishers](docs/creating-publishers.md)
- [Architecture (Hexagonal Design)](docs/architecture.md)
- [Hooks & Filters](docs/hooks.md)
- [Supported Languages](docs/translations.md)

---

## 📃 License

[MIT License](LICENSE)

---

## 💬 Credits

Made with ❤️ by [@N3XT0R](https://github.com/n3xt0r) – Contributions welcome!