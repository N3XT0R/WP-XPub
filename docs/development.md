# Developer Guide

This guide explains how to set up a local environment for contributing to **WP-XPub** and how to extend it.

## Setup

1. Clone the repository and install the dependencies:
   ```bash
   git clone https://github.com/N3XT0R/WP-XPub.git
   cd WP-XPub
   composer install
   ```

2. Link the plugin into your WordPress `plugins/` directory or load it via Bedrock's `mu-plugins` folder.

## Running Tests

The project uses PHPUnit for its test suite. After installing the dependencies run:

```bash
vendor/bin/phpunit
```

The PHPUnit configuration is stored in `phpunit.xml`.

## Dependency Injection Container

WP-XPub uses [PHP-DI](https://php-di.org/) to wire its services. The container is built by `ContainerProvider` from the
`\*ContainerConfigurator` classes under `src/*/DI/`.

You can register your own services or override existing ones by implementing the `ContainerConfiguratorInterface`:

```php
use DI\ContainerBuilder;
use N3XT0R\XPub\Shared\DI\ContainerConfiguratorInterface;

class MyConfigurator implements ContainerConfiguratorInterface
{
    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            // Your custom services
        ]);
    }
}
```

Pass your configurator instance to the plugin bootstrap before calling `boot()`.

