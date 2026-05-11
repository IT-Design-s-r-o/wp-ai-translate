[CHANGELOG.md](https://github.com/user-attachments/files/27601927/CHANGELOG.md)
# Changelog

All notable changes to **WP AI Translate** are documented here.

The project is currently in **Public Beta**. Until the first stable release, minor versions may include UX changes, compatibility fixes, and beta-only diagnostics.

## 0.3.23 - Menu Walker Compatibility Fix

### Fixed

- Fixed a fatal error when themes call the WordPress `nav_menu_link_attributes` filter with 3 arguments instead of 4.
- This only changes the menu language switcher compatibility layer.

### Notes

- No translation engine, queue, scanner, provider, routing, WooCommerce, frontend editor, database, shortcodes, or Translation Matrix logic was changed.

## 0.3.22 - Menu Switcher Anchor Protection

### Fixed

- Added menu-only protection directly to WordPress menu language links so the frontend URL rewriter cannot turn `?lang=en` or `?lang=ka` back into the currently selected language.
- This targets the menu language switcher only.

### Notes

- No translation engine, queue, scanner, provider, routing, WooCommerce, frontend editor, database, shortcodes, or Translation Matrix logic was changed.

## 0.3.21 - Menu Switcher Link Rewrite Fix

### Fixed

- Prevented the frontend language URL rewriter from overwriting WP menu language switcher links back to the currently selected language.
- Menu language switcher links such as `/?lang=en`, `/?lang=ka`, and `/?lang=ru` now keep their intended target language.

### Notes

- Only the menu language switcher link protection was changed. Translation engine, queue, scanner, provider, routing, WooCommerce, frontend editor, database, shortcodes, and Translation Matrix logic were not changed.

## 0.3.20 - Menu Switcher Behavior Fix

### Fixed

- Fixed menu language switcher behavior when the menu item was added as the current-language switcher.
- Current-language menu switcher now expands to all enabled languages, so visitors can switch from the first selected language to any other enabled language.

### Notes

- No translation engine, queue, scanner, provider, routing, WooCommerce, frontend editor, database, or Translation Matrix logic was changed.

## 0.3.19 - Menu Switcher Visibility Fix

### Fixed

- Improved registration of the **Language Switcher** metabox on the classic WordPress **Appearance > Menus** screen.
- Removed a duplicate HTML id inside the menu metabox markup and raised the metabox priority.

### Notes

- No translation engine, queue, scanner, provider, routing, WooCommerce, frontend editor, database, or Translation Matrix logic was changed.

## 0.3.18 - Public Beta Prep

### Added

- Expanded Elementor Language Switcher widget controls:
  - List, Dropdown, Buttons, Flags only, Flags + language name, and Language name only layouts.
  - Flag, language name, language code, current language, hide current language, alignment, and orientation options.
  - Basic Elementor style controls for typography, colors, background, borders, padding, item gap, dropdown width, and dropdown shadow.
- WordPress menu language switcher support via **Appearance > Menus**.
- Public Beta support and donation flow with `https://paypal.me/wpaitranslate`.
- Improved Support page with links to bug reports, feedback, documentation, plugin website, and contact details.
- Public Beta notice buttons for Report Bug, Send Feedback, and Support Development.

### Changed

- Replaced the plugin logo with the new `logo.png` asset.
- Improved Report Bug and Feedback form layouts with responsive three-column grids.
- Improved Setup Wizard API key fields to prevent horizontal overflow.
- Improved language search behavior in admin language lists.
- Refined frontend/menu switcher CSS without changing language URL generation.

### Fixed

- Admin form fields overlapping in Report Bug and Feedback pages.
- API key fields escaping the Setup Wizard card on narrower screens.
- Missing or inconsistent logo display in plugin admin areas and frontend editor panel.

### Notes

- Translation engine, queue processing, scanner logic, provider API logic, routing logic, WooCommerce compatibility, database schema, frontend editor core logic, and Translation Matrix architecture were not changed in this release.

## 0.3.17 - Public Beta UX

### Added

- Basic / Advanced interface mode.
- Onboarding wizard.
- Public Beta notices.
- Bug report and feedback pages.
- Safe debug log export.

### Changed

- Improved admin page organization for non-technical users.
- Prepared internal feature flag helpers for future Free / Professional editions.

## 0.3.16 - Stable Internal Beta

### Added

- Queued translation workflow.
- Scanner for posts, pages, products, menus, widgets, taxonomy terms, SEO meta, and public custom fields.
- Translation Matrix with pagination and bulk save.
- CSV, PO, and MO import/export.
- Google Translate, OpenAI, Gemini, Grok/xAI, and DeepL provider support.
- Frontend translation editor for administrators.
- WooCommerce product and category URL compatibility improvements.

### Notes

- This version became the internal stability baseline before Public Beta preparation.
