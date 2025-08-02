# Creating Custom Publishers

To create your own publisher, implement the `PublisherInterface` or extend `PublisherAbstract`.

## Option 1: Implement PublisherInterface

```php
class MyPublisher implements PublisherInterface {
    public function publish(Article $article): bool {
        // Custom publish logic
        return true;
    }
}
```

## Option 2: Extend PublisherAbstract

```php
class MyPublisher extends PublisherAbstract {
    protected string $slug = 'myPublisher';

    protected function handlePublish(Article $article): bool {
        $this->log("Publishing: " . $article->title);
        return true;
    }
}
```

## Step 2: Register via Hook

To register a custom publisher, hook into the `wp_xpub_factory_map` filter.
This hook allows you to add your own publisher classes to the list of available targets.

In your plugin or theme:

```php
add_filter('wp_xpub_factory_map', static function (array $map): array {
    $map['myPublisher'] = \YourNamespace\MyPublisher::class;
    return $map;
});
```

The internal factory uses this hook like this:

```php
private static function getDefaultPublisherArray(): array
{
    return [
        'devto' => DevToPublisher::class,
        'mastodon' => MastodonPublisher::class,
    ];
}

$map = apply_filters('wp_xpub_factory_map', self::getDefaultPublisherArray());
```

The key `('example')` acts as the slug, and the value must be the fully qualified class name of a concrete publisher
that extends `PublisherAbstract`

### OAuth Support

If your publisher needs OAuth credentials, implement
`SupportsOAuthFactoryInterface`. This allows the core `PublisherFactory` to
inject an `OAuthTokenProviderFactory` automatically. To skip writing the
required getter and setter, include the `SupportsOAuthFactoryTrait`.

```php
use N3XT0R\XPub\Domain\Publishers\Contracts\SupportsOAuthFactoryInterface;
use N3XT0R\XPub\Domain\Publishers\Traits\SupportsOAuthFactoryTrait;

class MyOAuthPublisher extends PublisherAbstract implements SupportsOAuthFactoryInterface
{
    use SupportsOAuthFactoryTrait;
}
```

