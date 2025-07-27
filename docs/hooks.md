# Hooks in WP-XPub

WP-XPub supports the following pre- and post-publish hooks for full customization.

## Available Hooks

### `xpub.before_publish`
Fires before a post is published.

**Example:**
```php
add_action('xpub.before_publish', function ($publisherSlug, $article) {
    // Custom logic before publishing
}, 10, 2);
```

### `xpub.after_publish`
Fires after a post is published.

**Example:**
```php
add_action('xpub.after_publish', function ($publisherSlug, $article, $success) {
    // Custom logic after publishing
}, 10, 3);
```
