# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2025-08-28

### Fixed

- admin gui save action fixed.

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
