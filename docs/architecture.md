# WP-XPub Architecture

WP-XPub is built with hexagonal architecture (Ports & Adapters).

## Layers

- **Domain** – Pure business logic
- **Infrastructure** – WordPress-specific code, APIs
- **Application** – Coordination, use cases
- **Support** – Utilities and helpers

## Structure

```
src/
├── Application/       # Use cases, factories, orchestration
├── Domain/            # Entities and contracts (PublisherInterface, Article, etc.)
├── Infrastructure/
│   ├── Publishers/    # Implementations of publishing platforms (Dev.to, etc.)
│   ├── Wordpress/     # WP-specific logic (hooks, admin, DB, logging)
├── Support/           # Reusable utilities (Seeder, helpers)
```
