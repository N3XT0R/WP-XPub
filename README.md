# WP-XPub

**Flexible Multi-Channel Auto Publisher for WordPress**

WP-XPub is a lightweight, extensible auto-publishing plugin for WordPress. It allows you to publish your posts to
multiple external platforms – like LinkedIn, Mastodon, Dev.to, and more – either immediately or via scheduled jobs.

---

## 🚀 Features

- 🔁 Auto-publish WordPress posts to external platforms
- 🧩 Modular driver system – easily extend to support new platforms
- 🕓 Schedule or trigger publishing via cron/hooks
- 🔄 Pre-/post-publish hooks for full customization
- 🔧 Clean architecture (PSR-4, SOLID)
- 📦 Composer-ready
- 🐘 No external services required

---

## ✅ Use Cases

- Automatically publish new blog posts to Dev.to, Mastodon, etc.
- Push content to personal or team knowledge platforms
- Integrate WordPress content into larger editorial workflows

---

## 📦 Installation

1. Install via the WP plugin manager or add to your Composer project
2. Configure your desired publishers under `Settings > WP-XPub`
3. Optionally trigger publishing via code or cron

---

## 🧑‍💻 Programmatic Usage

Trigger publishing manually:

```php
do_action('xpub_publish', $post_id);
