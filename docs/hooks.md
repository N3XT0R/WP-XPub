# Hooks in WP-XPub

WP-XPub fires hooks around the publish process for each configured publisher.

## Available Hooks

### `xpub_pre_publish_{slug}`
Filter executed before a post is published to a specific publisher. Replace `{slug}`
with the publisher's slug (for example `devto`).

**Example:**
```php
add_filter('xpub_pre_publish_devto', function ($article, $publisher) {
    // Modify the article before publishing to Dev.to
    return $article;
}, 10, 2);
```

### `xpub_post_publish_{slug}`
Action fired after a post has been published to a specific publisher.

**Example:**
```php
add_action('xpub_post_publish_devto', function ($article, $success, $publisher) {
    // Custom logic after publishing to Dev.to
}, 10, 3);
```
