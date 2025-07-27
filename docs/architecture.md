# WP-XPub Architecture

WP-XPub is built using the principles of **Hexagonal Architecture (Ports & Adapters)** to ensure a clean separation of
concerns, testability, and long-term maintainability.

---

## Why Hexagonal?

The goal of WP-XPub is **not** to be tightly coupled to WordPress internals – although it runs in a WP environment.
Instead, we aim to:

- **Encapsulate business logic**: The Domain layer is isolated and testable outside of WordPress.
- **Support multiple publish targets**: Each platform (e.g., Dev.to) is a replaceable adapter.
- **Enable better testing**: Application logic can be tested without bootstrapping WordPress.
- **Allow future evolution**: A headless or API-only variant could reuse Domain and Application layers.
- **Make WordPress a technical detail**, not the architectural core.

---

## Layers

### Domain

- Pure business logic.
- No knowledge of WordPress, HTTP, or infrastructure.
- Defines contracts such as:
    - `PublisherInterface`
    - `Article`
    - `TranslatesMessagesInterface`

### Application

- Coordinates flows (use cases).
- Uses domain objects and orchestrates infrastructure via interfaces.
- Contains factories and service classes like `ArticlePublisher`.

### Infrastructure

- Contains **all implementation details**, including:
    - WordPress API integration (hooks, post meta, filters)
    - Publishing adapters (e.g., Dev.to API)
    - Logging
    - Markdown rendering
- Implements domain/application interfaces.
- Replaceable at runtime or test time.

### Support

- Reusable helper classes (e.g., `Seeder`, MarkdownRenderer).
- Contains no domain or application logic.
- Provides shared functionality that is orthogonal to business logic.

---

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

---

## Extensibility

- **New publishers** can be registered via the `wp_xpub_factory_map` filter.
- **Custom content handling** is done via interfaces like `RendersPostContentInterface`.
- **Markdown conversion** is abstracted via a dedicated renderer.
- **Settings** are encapsulated and not directly accessed from the domain.

---

## Future-Proofing

This modular architecture allows:

- Reuse of core logic in other environments (e.g., Laravel, CLI apps).
- Swapping or upgrading infrastructure without touching business logic.
- Better testability and CI integration.
- Asynchronous publishing (e.g., job queues) without modifying domain behavior.