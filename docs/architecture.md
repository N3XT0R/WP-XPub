# WP-XPub Architecture

**WP-XPub is a native WordPress plugin** — just built with a clear internal structure.

We follow a layered architecture pattern inspired by modern PHP practices (sometimes called hexagonal or "Ports &
Adapters"). Why? Because it helps keep the code clean, testable, and easy to extend — even in large projects.

This is still 100% WordPress:

- It uses standard WordPress hooks and filters.
- It registers post meta fields the WordPress way.
- It integrates with the post editor UI.
- It respects WP roles, settings, permissions, and translation practices.

## Why a layered structure?

By separating logic into clear layers, we avoid mixing business logic with WordPress-specific code. That means:

- **Domain**: Core concepts like articles, publishers, and publishing logic.
- **Infrastructure**: WordPress-specific code (hooks, admin UI, persistence).
- **Application**: Orchestrates use cases and coordinates services.
- **Support**: Utilities like logging, seeding, or markdown rendering.

This way, we keep the WordPress integration where it belongs – but the rest of the plugin remains reusable and modular.

## Code Structure

```
src/
├── Adapter/          # WordPress bootstrap and cron wrappers
├── Application/      # Use cases, factories, orchestration
├── Domain/           # Entities and contracts (PublisherInterface, Article, etc.)
├── Infrastructure/
│   ├── Publishers/    # Implementations of publishing platforms (Dev.to, etc.)
│   ├── Wordpress/     # WP-specific logic (hooks, admin, DB, logging)
├── Shared/           # Plugin context, DI contracts
├── Support/          # Helpers for seeding and utilities
```

## Queue & Scheduling

When a post is saved, WP-XPub stores publishing jobs in its own queue table. A
WordPress cron task `xpub_run_job_runner` runs every minute (`xpub_every_minute`)
and processes pending jobs.

OAuth tokens are kept up to date by a second task `xpub_refresh_tokens`, which
is scheduled every ten minutes (`xpub_every_ten_minutes`).

Together these tasks ensure that articles are published asynchronously without
blocking the editor and that token-based publishers stay authenticated.

## OAuth-aware Publishers

Publishers that work with OAuth tokens signal support by implementing
`SupportsOAuthFactoryInterface`. The `PublisherFactory` automatically injects an
`OAuthTokenProviderFactory` for these classes. To reduce boilerplate, publishers
can include the `SupportsOAuthFactoryTrait`, which provides the required
getter/setter implementation.

## Dependency Injection

All services are wired through [PHP-DI](https://php-di.org/). The container is
built by `ContainerProvider` using the `*ContainerConfigurator` classes under
`src/*/DI/`.

You can register your own configurator implementing
`ContainerConfiguratorInterface` to override services or provide additional
definitions.

---

## Benefits

- 🔧 **Easy to maintain**: Each part of the code has its place.
- 🔌 **Easy to extend**: Add new publishing targets with just one adapter class.
- 📦 **Plugin-friendly**: Integrates cleanly into the WordPress plugin system.
- 🌍 **Future-proof**: Can be reused outside of WordPress if needed (headless CMS, CLI, etc).

---

Yes, it's a WordPress plugin.  
Yes, it uses namespaces, contracts, factories, and modern code.  
Yes, it works out of the box in any WordPress environment.

> _Just because it's cleanly built doesn't mean it's not native.
