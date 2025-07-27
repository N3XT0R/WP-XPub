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