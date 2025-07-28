# WP-XPub

[![CI](https://github.com/N3XT0R/wp-xpub/actions/workflows/ci.yml/badge.svg)](https://github.com/wp-xpub/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/n3xt0r/wp-xpub/v/stable)](https://packagist.org/packages/n3xt0r/wp-xpub)
[![Code Coverage](https://qlty.sh/gh/N3XT0R/projects/WP-XPub/coverage.svg)](https://qlty.sh/gh/N3XT0R/projects/WP-XPub)
[![Maintainability](https://qlty.sh/gh/N3XT0R/projects/WP-XPub/maintainability.svg)](https://qlty.sh/gh/N3XT0R/projects/WP-XPub)

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

[Installation](docs/installation.md)
Activate the plugin in WordPress and configure publishers via  
**Settings > XPUB**

> ⚠️ **Note**: WP-XPub is *not* available on the official WordPress Plugin Hub.  
> This is a conscious decision, as the WordPress plugin linter enforces coding patterns that violate modern Clean Code
> and architectural best practices.  
> WP-XPub adheres to a strict PSR-based code style and a hexagonal architecture, which cannot be reconciled with certain
> WordPress guidelines.

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