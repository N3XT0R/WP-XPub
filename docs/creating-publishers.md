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
