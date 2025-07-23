# WP-XPub

**Flexible Multi-Channel Auto Publisher for WordPress**

WP-XPub is a lightweight, extensible auto-publishing plugin for WordPress. It allows you to publish your posts to
multiple external platforms – including LinkedIn, Mastodon, Dev.to, and more – either immediately or via scheduled jobs.

---

## 🚀 Features

- 🔁 Auto-publish WordPress posts to external platforms
- 🧩 Modular client driver system (easily add new platforms)
- 🕓 Schedule publishing via WP Cron or hook-based triggers
- 🧵 Pre- and post-publish hooks for full customization
- 📦 Composer-ready (PSR-4 autoloading, modern architecture)
- 🐘 Built with PHP – no external API calls required, uses Monolog for local logging

---

## 🛠️ Use Cases

- Automatically share blog posts to Dev.to, Mastodon, or LinkedIn
- Publish to personal networks or headless CMS endpoints
- Integrate publishing into a CI/CD pipeline

---

## 📦 Installation

1. Download or require via Composer:

```bash
composer require n3xt0r/wp-xpub
```

2. Activate the plugin in WordPress

3. Configure your publishers via **Settings > XPUB**

---

## ✅ Requirements

- PHP 8.2+
- WordPress 6.8.2+
- Composer (optional for development)

---

## 🧱 Architecture

WP-XPub uses a clean, layered architecture with:

- Domain Models for `Article`, `Publisher`, etc.
- Contracts (Interfaces) for integration boundaries
- Infrastructure for WordPress implementation
- Support utilities (views, helpers, logging, etc.)

---

## 🪝 Actions & Filters

Trigger a publish via WordPress:

```php
do_action('xpub_publish', $post_id);
```

Register a new publisher class:

```php
add_filter('wp_xpub_factory_map', function (array $map) {
    $map['myplatform'] = \Vendor\Namespace\MyCustomPublisher::class;
    return $map;
});
```

Seed the publisher config:

```php
use N3XT0R\XPub\Support\PublisherSeederHelper;

PublisherSeederHelper::upsert('myplatform', 'My Platform', [
    'api_key' => '', // Required config
]);
```

---

## ✍️ Create Your Own Publisher

Implement the `PublisherInterface`:

```php
namespace Vendor\Namespace;

use N3XT0R\XPub\Domain\Contracts\PublisherInterface;
use N3XT0R\XPub\Domain\Entity\Article;

class MyCustomPublisher implements PublisherInterface
{
    public function publish(Article $article): bool
    {
        // Your API logic here
        return true;
    }
}
```

Or extend `PublisherAbstract` for logging/config support:

```php
class MyCustomPublisher extends \N3XT0R\XPub\Infrastructure\Publishers\PublisherAbstract
{
    public function publish(Article $article): bool
    {
        $this->log("Publishing: " . $article->title);
        return true;
    }
}
```

---

## 🧪 Testing

WP-XPub follows modern testing principles. To test your custom publishers:

```php
$publisher = PublisherFactory::createWithConfig('myplatform', [
    'api_key' => 'xyz'
]);

$publisher->publish(new Article('Title', 'Body'));
```

---

## 📁 Folder Structure

```
src/
├── Application/
├── Domain/
├── Infrastructure/
│   └── Wordpress/
├── Support/
└── ...
```

---

## 📃 License

[MIT License](LICENSE)

---

## 💬 Credits

Made with ❤️ by [@N3XT0R](https://github.com/n3xt0r) – Contributions welcome!