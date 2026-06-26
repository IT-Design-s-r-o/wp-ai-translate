# Changelog

All notable changes to AIT Multilingual Translate are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Planned

- Additional workflow demonstrations for the scanner, frontend editor, translation matrix, and queue.
- Expanded documentation for provider setup and production translation workflows.

## [0.3.34] - 2026-06-26

### Changed

- Updated DeepL Terms and Privacy Policy links for WordPress.org review.
- Confirmed the public plugin name as AIT Multilingual Translate.
- Prepared GitHub-facing documentation for the approved WordPress.org release.

## [0.3.33]

### Changed

- Renamed public plugin branding to AIT Multilingual Translate.
- Removed restricted or trademark-sensitive wording from the public plugin name.
- Updated public package metadata for the WordPress.org submission.
- Improved external services disclosure in `readme.txt`.
- Removed or corrected outdated external service legal and privacy links.

### Improved

- Improved WordPress enqueue handling for JavaScript and CSS.
- Improved plugin directory and path handling through WordPress helper functions.
- Improved output buffering lifecycle.
- Improved output escaping in frontend and admin rendering.
- Tested the updated package with Plugin Check.

### Fixed

- Removed custom updater and self-update override logic for WordPress.org compliance.
- Restored target language search in the dashboard and setup wizard after the public prefix cleanup.
- Restored frontend language switcher styling for shortcode, Elementor widget, automatic placement, and WordPress menu output.
- Restored frontend editor editable text detection and AJAX actions after the final public rename.
- Prepared the updated ZIP package for WordPress.org review.

## [0.3.32]

### Changed

- Updated the final plugin slug, folder, ZIP name, and text domain to `ait-multilingual-translate`.
- Removed previous public-facing naming from the installable package.

### Fixed

- Removed beta self-update and repair updater hooks for WordPress.org compliance.
- Replaced remaining direct admin inline script and style output with WordPress enqueue APIs.
- Updated generated log storage to use `wp_upload_dir()`.
- Clarified Google Translate disclosure.
- Added explicit output-buffer shutdown handling for review safety.

## [0.3.31]

### Changed

- Updated the public WordPress.org plugin name to AIT Multilingual Translate.
- Updated submission slug, text domain, package structure, language template filename, and public-facing branding for WordPress.org guidelines.

### Fixed

- Improved WordPress.org naming compliance.
- Reduced generic and trademark review risk with a distinct public brand.

## [0.3.30]

### Added

- Public Beta release metadata and package slug.
- Translation Mode and Tone of Voice foundation for prompt-based AI providers.
- AI Cost Optimization settings for quality mode, temperature, request character limits, estimated per-request cost limits, and model recommendations.
- Provider statistics for API requests, estimated tokens, estimated cost, cache hits, duplicates skipped, provider, and model.
- Provider capability cards for active providers and planned architecture targets.
- External services disclosure in `readme.txt`.

### Improved

- AI provider handling and provider diagnostics.
- Translation memory and deduplication before provider requests.
- Scanner and queue workflow visibility.
- Frontend editor UX, loading states, and manual-save flow.
- Language switcher UI and local flag handling.
- Elementor widget display options.
- Setup wizard and dashboard organization.
- WordPress text-domain loading for language-pack readiness.

### Fixed

- Plugin Check errors for WordPress.org preparation.
- SQL prepared statement issues in reviewer-sensitive areas.
- Sanitization, escaping, and nonce handling in admin and frontend actions.
- ZIP package structure for the public plugin slug.
- Public Beta wording and older commercial-facing text.
- Developer-only support notes excluded from the installable ZIP.

## [0.3.29]

### Added

- Translation Style and Tone of Voice settings section for the Public Beta release.
- Translation Mode visibility in scanner and queue statistics.

### Changed

- Documented that per-content-type modes are planned for a future release while the current build uses Global Translation Mode.

## [0.3.28]

### Added

- Scanner and Queue "Translate All" action for processing queued translations in safe provider batches.
- Global Translation Mode and Tone of Voice UI copy.
- Server-side custom instruction length protection.

## [0.3.27]

### Fixed

- Admin asset loading for plugin subpages after the public slug change.
- Remaining public Elementor switcher label using outdated branding.
- Modular settings link to the top-level admin page.

## [0.3.26]

### Added

- Public WordPress.org package name and metadata.
- Translation Mode and Tone of Voice settings for prompt-based AI providers.
- `languages/` folder required by the plugin header.

### Changed

- Default OpenAI model changed to `gpt-4o-mini`.
- Default xAI / Grok model changed to a lightweight default where available.

### Fixed

- Hardened custom-table SQL and local file handling for Plugin Check compatibility.

## [0.3.25]

### Added

- Secure frontend editor Auto Translate workflow.

### Improved

- Frontend editor modal states, textarea resizing, and provider error messaging.
- Manual save flow so AI translation is previewed and edited before saving.

## [0.3.24]

### Improved

- WordPress.org preparation cleanup.
- URL parsing through WordPress helper APIs.
- Uninstall variable prefixing and translator comments.

## [0.3.23]

### Added

- Public Beta Build prepared for WordPress.org review.
- WordPress.org `readme.txt`.
- GPL metadata.
- External service disclosures.

### Changed

- Replaced earlier beta wording with Public Beta Build wording.
