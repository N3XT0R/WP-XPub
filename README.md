# WP-XPub

**Flexible Multi-Channel Auto Publisher for WordPress**

WP-XPub is a lightweight, extensible auto-publishing plugin for WordPress. It allows you to publish your posts to multiple external platforms with ease – including LinkedIn, Mastodon, Dev.to, and more – either immediately or via scheduled jobs.

## Features

- 🔁 Auto-publish WordPress posts to external platforms
- 🧩 Modular client driver system (easily add support for new platforms)
- 🕓 Schedule or trigger publishing via cron or hooks
- 🧵 Pre- and post-publish hooks for full customization
- 📦 Composer-ready (PSR-4 support, clean architecture)
- 🐘 Built with PHP – no third-party service required

## Use Cases

- Automatically share new blog posts to Dev.to or LinkedIn
- Push content to Mastodon or your personal content network
- Integrate publishing into a larger CI/CD flow

## Installation

1. Install via plugin upload or include in your project
2. Configure your preferred channels and keys
3. Enable auto-publishing or use programmatically

## Requirements

- PHP 8.0+
- WordPress 5.8+
- Optional: Composer (for local development)

## Development

WP-XPub follows modern coding standards and encourages clean separation of concerns. Use the included plugin hooks and client interface to build your own integrations.

```php
do_action('xpub_publish', $post_id);
