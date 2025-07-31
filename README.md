# WP-XPub

[![CI](https://github.com/N3XT0R/wp-xpub/actions/workflows/ci.yml/badge.svg)](https://github.com/N3XT0R/wp-xpub/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/n3xt0r/wp-xpub/v/stable)](https://packagist.org/packages/n3xt0r/wp-xpub)
[![Code Coverage](https://qlty.sh/gh/N3XT0R/projects/WP-XPub/coverage.svg)](https://qlty.sh/gh/N3XT0R/projects/WP-XPub)
[![Maintainability](https://qlty.sh/gh/N3XT0R/projects/WP-XPub/maintainability.svg)](https://qlty.sh/gh/N3XT0R/projects/WP-XPub)

**Flexible Multi-Channel Auto Publisher for WordPress**
Publish your blog posts to Dev.to or any custom target – automatically and without writing a single line of code.

---

## ✨ What is WP-XPub?

**WP-XPub** is a lightweight, ready-to-use WordPress plugin that lets you automatically share your posts to multiple
external platforms – either instantly or via scheduled jobs.

✅ **No technical knowledge needed** – just install, activate, connect your accounts and you're done.  
🛠️ **Developers welcome** – WP-XPub is modular, PSR-compliant, and follows a clean hexagonal architecture.

---

## 🚀 Features

- 🔁 Auto-publish WordPress posts to external platforms
- 🔌 Built-in Dev.to driver (Mastodon example included) – extendable via plugin drivers
- 🧩 Modular client driver system (easily extend to new platforms)
- ⏱️ Publish immediately or schedule for later
- 🧵 Custom pre- and post-publish hooks
- 📦 Composer-ready (PSR-4 autoloading, modern structure)
- 🐘 Local logging via Monolog – no external tracking or APIs
- 🔄 GitHub update integration with custom updater hooks

---

## 📦 Installation

1. Clone or download the plugin into your `wp-content/plugins` directory.
2. Activate it via the WordPress admin panel.
3. Go to **Settings > XPUB** and connect your desired platforms.

📄 See the full [installation guide](docs/installation.md) for details.

> ⚠️ **Note:** WP-XPub is *not* listed on the official WordPress Plugin Directory.  
> This is by design: WordPress.org enforces legacy coding patterns that conflict with modern PSR standards and clean
> architecture.  
> WP-XPub prioritizes maintainability and extensibility over legacy compatibility.
>
> 🛠️ **Why this deviation?**  
> WP-XPub follows a hexagonal (ports & adapters) architecture to enforce separation of concerns, testability, and
> long-term maintainability.  
> The WordPress Plugin Directory imposes structural constraints that prevent clean software design, including:
>
> - Reliance on `functions.php` and global functions instead of DI and modular bootstrapping
> - Static hook registration without lifecycle encapsulation
> - No support for PSR-4, namespaces, or autoloading
> - **A legacy translation system** (`gettext`) that requires **literal strings** for all translatable text – making
    dynamic or domain-driven I18n impossible
> - No support for application-layer abstractions (e.g. service containers, middleware, or event buses)
>
> ❌ These constraints hinder composability, reusability, and testability.
>
> ✅ WP-XPub deliberately separates domain logic, infrastructure, and framework adapters – allowing modern PHP practices
> like:
>
> - **Constructor-based dependency injection**
> - **PSR-compliant, autoloaded class structure**
> - **Dynamic, context-aware translations via service-based I18n**
> - **Full test coverage of application and domain code – independent from WordPress internals**
>
> WP-XPub is designed for developers who want to **integrate WordPress without being constrained by it**.

---

## ✅ Requirements

- PHP 8.2+
- WordPress 6.0+ (tested with 6.8.2)
- Composer (optional, for development or extensions)

---

## 🧠 For Developers

WP-XPub is built with a clear separation of concerns and is easy to extend:

- 🧱 Hexagonal architecture (Ports & Adapters)
- 🧩 Create your own publisher drivers with minimal boilerplate
- 📦 Fully PSR-4 compliant, Composer-ready
- 🧪 CI integration and code coverage metrics
- 🐘 Monolog-based logging system
- 🔄 GitHub update workflow with changelog diffing

See the [developer docs](docs/index.md) for more:

- [Creating Custom Publishers](docs/creating-publishers.md)
- [Architecture Overview](docs/architecture.md)
- [Hooks & Filters](docs/hooks.md)
- [Translations](docs/translations.md)
- [Development Guide](docs/development.md)

---

## 📚 Full Documentation

All documentation is available in the `docs/` folder:

- [Overview & Index](docs/index.md)
- [Installation](docs/installation.md)
- [Update](docs/update.md)
- [Creating Publishers](docs/creating-publishers.md)
- [Architecture](docs/architecture.md)
- [Hooks & Filters](docs/hooks.md)
- [Languages](docs/translations.md)
- [Development Guide](docs/development.md)

---

## 📃 License

[MIT License](LICENSE)

---

## 💬 Credits

Made with ❤️ by [@N3XT0R](https://github.com/n3xt0r) – Contributions welcome!
