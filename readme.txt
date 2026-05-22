=== WPAIT Multilingual AI Translate ===
Contributors: itdesignsro
Donate link: https://paypal.me/wpaitranslate
Tags: translation, ai, multilingual, ecommerce, localization
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 0.3.31
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI multilingual translation with saved translations, SEO URLs, frontend editing, queues, and WooCommerce/Elementor compatibility.

== Description ==

WPAIT Multilingual AI Translate helps site owners translate WordPress content with AI and machine translation providers while saving translations locally for reuse.

The plugin is currently available as a Public Beta Build. Please make a backup before bulk translating production websites.

Language flag icons are bundled locally from the MIT-licensed lipis/flag-icons project. No external request is made to load flag images.

= Key features =

* Select source and target languages.
* Translate with OpenAI, Google Translate, DeepL, Gemini, or Grok/xAI.
* Choose a global Translation Mode / Tone of Voice for AI providers such as SEO Optimized, Marketing, eCommerce, Technical, Legal, Luxury Brand, Friendly, or Custom Prompt.
* Use AI Cost Optimization settings for Cheap, Balanced, or Premium model guidance, temperature control, request limits, token estimates, cache hits, and duplicate-skipped statistics.
* Store translations locally and reuse them until source text changes.
* Scan posts, pages, products, menus, widgets, taxonomy terms, SEO meta, and public custom fields.
* Process translation work in a queue so visitors do not wait for provider calls.
* Edit saved translations from the frontend as an administrator.
* Manage translations in a searchable Translation Matrix.
* Export and import translations as CSV, PO, or MO files.
* Add language switchers with shortcodes, widgets, Elementor, WordPress menus, header, or footer placement.
* Use directory URLs such as `/ka/about/` or query URLs such as `/about/?lang=ka`.
* Includes WooCommerce product and category URL compatibility helpers.
* Includes a debugger, route diagnostics, safe log export, and provider test tools.

= Public Beta =

WPAIT Multilingual AI Translate is still under active testing. Bulk translation can consume provider quota and should be tested on staging first. The plugin stores API keys in WordPress options unless you define keys in `wp-config.php`.

= Trademark notice =

WooCommerce and Elementor are trademarks of their respective owners. This plugin is not affiliated with, endorsed by, or sponsored by WooCommerce, Automattic, Elementor, or their parent companies.

= External services =

WPAIT Multilingual AI Translate can connect to external translation providers. No provider is contacted unless you configure a provider key and start a provider test, queue process, background queue run, or on-page translation request.

For all providers, the plugin may send source text strings, source language, target language, provider model/endpoint selection, and request metadata required by the provider. API keys are sent only to the selected provider endpoint and are not shown in the debugger or public output.

OpenAI:

* Sends selected strings and language instructions to OpenAI when OpenAI is the active provider.
* Terms: https://openai.com/policies/terms-of-use/
* Privacy: https://openai.com/policies/privacy-policy/

Google Translate:

* Sends selected strings plus source and target language codes to Google Cloud Translation when Google Translate is the active provider.
* Terms: https://cloud.google.com/terms
* Privacy: https://policies.google.com/privacy

DeepL:

* Sends selected strings plus source and target language codes to DeepL when DeepL is the active provider.
* Terms: https://www.deepl.com/terms-and-conditions
* Privacy: https://www.deepl.com/privacy

Gemini:

* Sends selected strings and language instructions to the Gemini API when Gemini is the active provider.
* Terms: https://ai.google.dev/gemini-api/terms
* Privacy: https://policies.google.com/privacy

Grok/xAI:

* Sends selected strings and language instructions to xAI when Grok/xAI is the active provider.
* Terms: https://x.ai/legal/terms-of-service
* Privacy: https://x.ai/legal/privacy-policy

Claude:

* Claude provider support is planned for a future release. When enabled in a future version, selected strings and language instructions may be sent to Anthropic only after the site owner configures an API key.
* Terms: https://www.anthropic.com/legal/commercial-terms
* Privacy: https://www.anthropic.com/legal/privacy

Yandex Translate / YandexGPT:

* Yandex provider support is planned for a future release. When enabled in a future version, selected strings, source language, target language, and language instructions may be sent to Yandex only after the site owner configures an API key.
* Terms: https://yandex.com/legal/cloud_terms/
* Privacy: https://yandex.com/legal/confidential/

== Installation ==

1. Upload the `wpait-multilingual-ai-translate` folder to `/wp-content/plugins/` or install the ZIP through **Plugins > Add New > Upload Plugin**.
2. Activate **WPAIT Multilingual AI Translate**.
3. Open **AI Translate** in the WordPress admin menu.
4. Choose source and target languages.
5. Select a provider and add the provider API key.
6. Run the scanner to collect site strings.
7. Process the translation queue.
8. Add a language switcher using `[wp_ai_translate_switcher]`, `[ai_language_switcher]`, a widget, Elementor, or a WordPress menu item.

== Frequently Asked Questions ==

= Does WPAIT Multilingual AI Translate translate pages on every visit? =

No. The recommended workflow is to scan strings, process them in the queue, save translations locally, and serve saved translations to visitors.

= Can I use the plugin without an AI provider? =

Yes. You can scan strings and manually edit translations in the Translation Matrix. Provider translation requires a provider API key.

= Does the plugin support WooCommerce? =

The Public Beta Build includes WooCommerce product and product-category URL compatibility helpers and scanner coverage for product content. Always test stores on staging before bulk translation.

= Where are translations stored? =

Translations are stored in a custom WordPress database table named with the site prefix, usually `wp_wpait_translations`.

= Are API keys visible in logs? =

The debugger masks provider keys. Safe debug exports redact secrets and sensitive data before inclusion in reports.

= Can I define API keys in wp-config.php? =

Yes. Supported constants are `WPAIT_OPENAI_API_KEY`, `WPAIT_GEMINI_API_KEY`, `WPAIT_GROK_API_KEY`, `WPAIT_GOOGLE_TRANSLATE_API_KEY`, and `WPAIT_DEEPL_API_KEY`.

== Screenshots ==

1. Dashboard with Public Beta notice and quick setup areas.
2. Setup Wizard for languages, providers, API keys, scanning, and frontend editor.
3. Translation Matrix with search, status filters, pagination, and bulk save.
4. Scanner and queue controls for collecting and translating strings in batches.
5. Frontend editor toolbar and modal for administrator translation corrections.
6. Elementor language switcher widget controls.
7. WordPress menu language switcher setup.
8. Debugger with provider test, route diagnostics, queue status, and safe log export.

== Changelog ==

= 0.3.31 =

* Renamed the public WordPress.org plugin name to WPAIT Multilingual AI Translate.
* Updated the submission slug and package root to `wpait-multilingual-ai-translate`.
* Updated the text domain and language template filename for WordPress.org language-pack readiness.
* Updated public-facing branding, readme text, and WordPress.org trademark wording.
* Kept internal prefixes, option names, database tables, shortcodes, translation engine, queue, scanner, routing, Elementor widget, frontend editor, and WooCommerce compatibility logic unchanged.

= 0.3.30 =

* Added AI Cost Optimization settings for quality mode, temperature, request character limits, estimated per-request cost limits, model guidance, and token/cost statistics.
* Added safe deduplication before provider requests and translation-memory cache-hit handling for frontend auto-translate.
* Added provider capability cards for active providers and planned architecture targets without enabling unfinished provider API integrations.
* Loaded the WordPress text domain for language-pack readiness and disabled the older internal dictionary fallback.
* Updated the public package slug to `wpait-multilingual-ai-translate`.
* Kept translation engine, queue architecture, scanner collection logic, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.29 =

* Finalized the Translation Style / Tone of Voice settings section for the Public Beta release.
* Added Translation Mode visibility to Scanner and Queue statistics.
* Documented that per content type modes are planned for a future release while the current build uses Global Translation Mode.
* Kept translation engine, queue architecture, scanner collection logic, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.28 =

* Added a Scanner and Queue "Translate All" action that processes queued translations in safe provider batches with quota/time-limit guidance.
* Finalized Global Translation Mode / Tone of Voice UI copy and server-side custom instruction length protection.
* Kept translation engine, queue architecture, scanner collection logic, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.27 =

* Fixed admin asset loading for all plugin subpages after the public slug change.
* Replaced the remaining public Elementor switcher label that used the old plugin name.
* Updated the modular settings link to the new top-level admin page.
* Kept translation engine, queue, scanner, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.26 =

* Prepared the public WordPress.org package name, metadata, and build structure for WPAIT Multilingual AI Translate.
* Added Translation Mode / Tone of Voice settings for prompt-based AI providers.
* Switched default OpenAI model to `gpt-4o-mini` and Grok default to a lightweight model where available.
* Hardened custom-table SQL and local file handling for WordPress.org Plugin Check compatibility.
* Kept translation engine, queue, scanner, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.25 =

* Added secure frontend editor Auto Translate so administrators can request an AI/provider translation inside the edit modal, review it, and save manually.
* Improved frontend editor modal loading state, textarea auto-resize, mobile spacing, and provider error messaging.
* Kept translation engine, queue, scanner, routing, WooCommerce handling, translation matrix, and language switcher behavior unchanged.

= 0.3.24 =

* Replaced remaining `parse_url()` calls with WordPress `wp_parse_url()` for Plugin Check compatibility.
* Added translator comments and documented safe internal HTML output paths.
* Improved uninstall variable prefixing for WordPress Coding Standards.
* Kept translation engine, queue, scanner, routing, WooCommerce handling, and language switcher behavior unchanged.

= 0.3.23 =

* Public Beta Build prepared for WordPress.org submission.
* Added WordPress.org `readme.txt`.
* Added GPL license file and plugin header license metadata.
* Replaced earlier beta wording with "Public Beta Build".
* Hardened uninstall table-name handling.
* Preserved translation engine, queue processing, scanner, routing, provider logic, frontend editor, WooCommerce compatibility, Elementor widget logic, and language switcher core logic.

= 0.3.22 =

* Added menu language switcher anchor protection for themes that rewrite menu links.

= 0.3.21 =

* Prevented the frontend language URL rewriter from overwriting WordPress menu language switcher links.

= 0.3.20 =

* Improved menu language switcher behavior for current-language switcher items.

== Upgrade Notice ==

= 0.3.31 =

Renames the public WordPress.org package to WPAIT Multilingual AI Translate and updates the slug/text domain. Test on staging before replacing an installed beta build.

= 0.3.30 =

Adds AI Cost Optimization, provider capability cards, language-pack readiness, and the final public package slug. Test on staging before bulk translation.

= 0.3.29 =

Final Tone of Voice polish and Scanner/Queue visibility for the selected global translation mode.

= 0.3.28 =

Adds a safe Scanner and Queue "Translate All" action plus final Tone of Voice UI polish. Test provider quota on staging before bulk translation.

= 0.3.27 =

Fixes admin styling on plugin subpages after the public slug change. No translation-engine or routing changes.

= 0.3.26 =

Public Beta Build package cleanup for WordPress.org, with Tone of Voice settings and Plugin Check hardening.

= 0.3.25 =

Adds frontend editor Auto Translate without changing queue, routing, WooCommerce, or switcher logic.

= 0.3.24 =

Controlled WordPress.org preparation cleanup. No translation-engine or routing changes.

= 0.3.23 =

Public Beta Build for WordPress.org preparation. Back up production websites before bulk translating and test provider quota settings on staging first.
