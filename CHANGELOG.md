# Changelog

All notable changes to WPAIT – AI Translate for WooCommerce & Elementor are documented here.

## 0.3.31

### Changed

- Renamed public WordPress.org plugin name to WPAIT – AI Translate for WooCommerce & Elementor.
- Updated submission slug to `wpait-ai-translate-for-woocommerce-elementor`.
- Updated text domain and WordPress.org package structure.
- Updated `readme.txt`, language template filename, and public-facing branding for WordPress.org guidelines.

### Fixed

- Improved WordPress.org naming compliance.
- Reduced generic/trademark review risk with a distinct WPAIT brand prefix.

### Compatibility note

- Kept internal prefixes, option names, database tables, hooks, shortcodes, translation engine, queue processing, scanner, routing, provider calls, frontend editor, Elementor widget, language switcher, and WooCommerce compatibility logic unchanged.

## 0.3.30

### Added

- WordPress.org-ready public plugin name: WPAIT – AI Translate for WooCommerce & Elementor.
- Public Beta release metadata and package slug: `wpait-ai-translate-for-woocommerce-elementor`.
- Tone of Voice / Translation Mode foundation for prompt-based AI providers.
- AI Cost Optimization settings for quality mode, temperature, request character limits, estimated per-request cost limits, and model recommendations.
- Provider statistics for API requests, estimated input/output tokens, estimated cost, cache hits, duplicate skipped count, provider, and model.
- Provider capability cards for active providers and planned architecture targets without enabling unfinished provider APIs.
- Frontend auto-translation improvements for the visual translation editor.
- External services disclosure in `readme.txt`.

### Improved

- AI provider handling and provider diagnostics.
- Token/cost optimization foundation.
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
- Sanitization, escaping, and nonce handling in admin/front-end actions.
- ZIP package structure for the public plugin slug.
- Public Beta wording and older Professional/Pro-facing text.
- The WordPress.org package now excludes developer-only support notes from the installable ZIP.

### Compatibility note

- Kept translation engine, queue processing architecture, scanner collection logic, routing, WooCommerce compatibility, frontend editor core logic, and language switcher core logic unchanged.

## 0.3.29

- Finalized the Translation Style / Tone of Voice settings section for the Public Beta release.
- Added Translation Mode visibility to Scanner and Queue statistics.
- Documented that per content type modes are planned for a future release while the current build uses Global Translation Mode.
- Kept translation engine, queue processing architecture, scanner collection logic, routing, WooCommerce compatibility, frontend editor core logic, and language switcher core logic unchanged.

## 0.3.28

- Added a Scanner and Queue "Translate All" action that processes queued translations in safe provider batches with quota/time-limit guidance.
- Finalized Global Translation Mode / Tone of Voice UI copy and server-side custom instruction length protection.
- Kept translation engine, queue processing architecture, scanner collection logic, routing, WooCommerce compatibility, frontend editor core logic, and language switcher core logic unchanged.

## 0.3.27

- Fixed admin asset loading for all plugin subpages after the public slug change.
- Replaced the remaining public Elementor switcher label that used the old plugin name.
- Updated the modular settings link to the new top-level admin page.
- Kept translation engine, queue processing, scanner, routing, WooCommerce compatibility, frontend editor core logic, and language switcher core logic unchanged.

## 0.3.26

- Prepared the public WordPress.org package name and metadata.
- Added Translation Mode / Tone of Voice settings for prompt-based AI providers.
- Changed the default OpenAI model to `gpt-4o-mini`.
- Changed the default Grok/xAI model to a lightweight default where available.
- Hardened custom-table SQL and local file handling for Plugin Check compatibility.
- Added the `languages/` folder required by the plugin header.
- Kept translation engine, queue processing, scanner, routing, WooCommerce compatibility, frontend editor core logic, and language switcher core logic unchanged.

## 0.3.25

- Added secure frontend editor Auto Translate.
- Improved frontend editor modal states, textarea resizing, and provider error messaging.
- Preserved manual save flow: AI translation is previewed and edited before saving.

## 0.3.24

- Improved WordPress.org preparation cleanup.
- Replaced remaining `parse_url()` usage with `wp_parse_url()`.
- Improved uninstall variable prefixing and translator comments.

## 0.3.23

- Prepared the Public Beta Build for WordPress.org review.
- Added WordPress.org `readme.txt`, GPL metadata, and external service disclosures.
- Replaced earlier beta wording with Public Beta Build wording.
