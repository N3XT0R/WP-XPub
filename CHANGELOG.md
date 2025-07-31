# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-07-31

### Added

- OAuth integration with REST endpoints and token provider factory.
- Dependency injection container setup using PHP-DI with `ContainerProvider` and `*ContainerConfigurator` classes.
- Documentation covering OAuth usage.

### Changed

- Refactored publishers and service classes to use dependency injection.
- Updated docs index to link to OAuth guide.

### Internal

- Added comprehensive unit and integration tests.

## [1.1.1] - 2025-07-29

### Fixed

- changed `ReleaseService` - replaced zipball_url against browser_download_url

## [1.1.0] - 2025-07-29

### Added

- Introduced asynchronous job system with new `Job` entity to represent deferred publishing tasks.
- Implemented `AsyncPublishingDispatcher` to enqueue one job per configured publisher.
- Added `PublisherSelector` for resolving all available publishers dynamically.
- Integrated WordPress cron via `WordpressCron` class, with custom 5-minute schedule (`xpub_every_five_minutes`) and
  automatic hook registration.
- Extended `Article` entity with `scheduledAt` timestamp, derived from WordPress `post_date_gmt`.
- Introduced new `JobRunner` that processes enqueued jobs via cron execution.

### Changed

- Publishing flow now handled asynchronously on post publish instead of immediate execution.
- `WordpressPlugin::handlePublishFromPost()` now uses `AsyncPublishingDispatcher` to decouple publishing from request
  lifecycle.

### Internal

- Manual dependency wiring added for cron job execution (no DI container yet).
- Clearer separation of plugin lifecycle logic (`init`, `boot`, `onActivate`, `onUninstall`) in `WordpressPlugin`.

## [1.0.1] - 2025-08-28

### Changed

- Changed from GPLv3 to MIT license
- modified readme.

## [1.0.0] – 2025-07-27

### Added

- Initial WordPress plugin structure with plugin hooks and registrars.
- `HookDefinition`, `HookProvider`, and `WordpressHookRegistrar` classes for hook management.
- `WordpressPublisherFactory` with support for configurable publishers.
- Base `View` rendering system using dot-notation and partials.
- Integration with PSR-3 `LoggerInterface` via `LoggerFactory`.
- `PublisherFactory` with support for configurable publisher classes (`DevToPublisher`).
- Basic unit test coverage (~43%) for core components.

### Changed

- Made `View::BASE_PATH` configurable via `setBasePath()` to support testability and decoupling.

### Fixed

- Logging behavior on misconfigured publisher targets now triggers a PSR-3 compatible logger error.
- Refactored short PHP echo tags for WordPress.org compatibility.
- Removed deprecated `load_plugin_textdomain()` call to meet current translation standards.
- Excluded hidden files from build package to comply with WP.org guidelines.

### Notes

- This is a **Release Candidate**: the plugin is functionally complete and API-stable, but not fully tested.
- Remaining test coverage and potential refactorings will be addressed in upcoming stable releases.
