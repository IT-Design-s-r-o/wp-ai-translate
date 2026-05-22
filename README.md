# AIT Multilingual Translate

**Powered by AITMT technology.**

WordPress.org submission name: **AIT Multilingual Translate**. Product brand: **AITMT**.

AIT Multilingual Translate is an AI-powered multilingual translation plugin for WordPress. It focuses on saved translation memory, queue-based provider usage, WooCommerce compatibility, Elementor workflows, frontend editing, SEO-friendly language URLs, and practical diagnostics for real sites.

Current public beta: **0.3.32 Public Beta Build**.

- Plugin website: https://wp-ai.itdesign.biz/
- Documentation: https://wp-ai.itdesign.biz/documentation/
- Live demo: https://wp-ai-demo.itdesign.biz/
- GitHub releases: https://github.com/IT-Design-s-r-o/wp-ai-translate/releases
- Support development: https://www.paypal.com/paypalme/wpaitranslate

## Public Beta Notice

AIT Multilingual Translate is currently available as a Public Beta. Please test on a staging website before using it on production projects, especially stores with WooCommerce orders, custom checkout fields, or complex page builders.

Public Beta builds include temporary full feature access while the plugin is actively tested and improved. Users who support the project during the Public Beta period may receive a special early-supporter offer for the future commercial release. No lifetime free access is promised.

WooCommerce and Elementor are trademarks of their respective owners. This project is not affiliated with, endorsed by, or sponsored by WooCommerce, Automattic, Elementor, or their parent companies.

## Video Tutorials

### Installation & Setup Wizard

Learn how to install AIT Multilingual Translate, configure languages, set up AI providers, and run the first translation workflow.

Watch: https://youtu.be/s8KnOtqXAFI

### Frontend Translation Editor

See how to visually edit frontend translations, use AI auto-translation, preview results, and save translated content directly from the website frontend.

Watch: https://youtu.be/sJE8FHwLk4s

### Language Workflow, Menu, Scanner & Elementor

Full workflow demo: add a new language, add the language switcher to a menu, scan content, process translations, and configure the Elementor language switcher.

Watch: https://youtu.be/MuUF4t6NNsA

## Features

- Source language can follow the WordPress site language automatically.
- Site owners choose target languages from a broad language list.
- Directory URL mode (`/ka/about/`) and query URL mode (`/about/?lang=ka`) are available.
- Selected visitor language is remembered with a cookie.
- OpenAI, Google Translate, DeepL, Gemini, and Grok/xAI providers are supported.
- Translation Mode / Tone of Voice foundation for prompt-based AI providers.
- AI Cost Optimization settings for quality mode, temperature, request limits, and model recommendations.
- Local provider quota controls can stop translation before provider-side quota errors.
- Translations are stored locally and reused until source text changes.
- Missing rendered frontend text is collected into a queue instead of being translated on every page load.
- Scanner can collect posts, pages, products, menus, widgets, taxonomy terms, SEO meta, and public custom fields.
- Queue can be processed manually or by WP-Cron in small controlled batches.
- Translation Matrix supports scanning, bulk save, search, status filters, pagination, and import/export.
- CSV, PO, and MO export/import are available per target language.
- Language switcher is available as shortcode, widget, Elementor widget, WordPress menu item, and optional header/footer placement.
- Local flag assets are bundled; no flag CDN is required.
- Administrator frontend editing supports manual editing and provider-based auto-translate.
- Debugger includes provider tests, route information, queue status, safe log export, and cost statistics.

## Installation

1. Download the latest release ZIP from GitHub Releases.
2. In WordPress admin, open **Plugins > Add New > Upload Plugin**.
3. Upload `ait-multilingual-translate.zip`.
4. Activate **AIT Multilingual Translate**.
5. Open **AI Translate** in the WordPress admin menu.
6. Choose source and target languages.
7. Select a provider and add the matching API key.
8. Run **Scanner** to collect strings.
9. Process the translation queue.
10. Add the language switcher with a shortcode, widget, Elementor widget, or WordPress menu item.

## Shortcodes

```text
[aitmt_language_switcher]
```

## API Key Constants

API keys can be stored in plugin settings or defined in `wp-config.php`:

```php
define( 'AITMT_OPENAI_API_KEY', 'your-api-key' );
define( 'AITMT_GEMINI_API_KEY', 'your-api-key' );
define( 'AITMT_GROK_API_KEY', 'your-api-key' );
define( 'AITMT_GOOGLE_TRANSLATE_API_KEY', 'your-api-key' );
define( 'AITMT_DEEPL_API_KEY', 'your-api-key' );
```

## Provider Notes

- DeepL Free uses `api-free.deepl.com`; DeepL paid API accounts use `api.deepl.com`.
- Google Translate uses Cloud Translation Basic v2 and requires the Cloud Translation API to be enabled in Google Cloud.
- Tone of Voice applies only to prompt-based AI providers.
- Keep "Translate missing strings during page load" disabled on production sites. Use the queue instead.
- Automatic background queue processing can use API quota. Keep batch size conservative until limits are confirmed.

## Documentation

Recommended documentation sections:

- Installation
- Setup Wizard
- AI Providers
- OpenAI Setup
- Gemini Setup
- Grok / xAI Setup
- Claude Setup
- DeepL Setup
- Google Translate Setup
- Yandex Translate Setup
- Frontend Translation Editor
- Language Switcher
- WordPress Menu Language Switcher
- Elementor Widget
- Scanner & Queue
- Translation Matrix
- WooCommerce Translation
- SEO URLs
- Tone of Voice / Translation Mode
- API Cost Optimization
- Troubleshooting
- FAQ
- Changelog

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer
- WooCommerce optional
- Elementor optional

## Support

- Website: https://wp-ai.itdesign.biz/
- Documentation: https://wp-ai.itdesign.biz/documentation/
- Demo: https://wp-ai-demo.itdesign.biz/
- Email: info@itdesign.biz
- Donation: https://www.paypal.com/paypalme/wpaitranslate

## License

GPLv2 or later.

https://www.gnu.org/licenses/gpl-2.0.html
