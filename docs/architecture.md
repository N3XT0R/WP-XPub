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

## Queue & Scheduling

When a post is saved, WP-XPub stores publishing jobs in its own queue table. A
WordPress cron task runs every minute and processes pending jobs. This ensures
that articles are published asynchronously without blocking the editor.

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
