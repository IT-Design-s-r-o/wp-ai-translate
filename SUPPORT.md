[SUPPORT.md](https://github.com/user-attachments/files/27601846/SUPPORT.md)
# Support

Thank you for testing **WP AI Translate**.

WP AI Translate is currently available as a **free Public Beta**. Please test on a staging site first and make a backup before bulk translating production websites.

## Where To Get Help

- Plugin website: https://wp-ai.itdesign.biz
- Documentation: https://wp-ai.itdesign.biz/documentation/
- Support email: info@itdesign.biz
- Support development: https://paypal.me/wpaitranslate

## Reporting Bugs

The preferred way to report a bug is from the WordPress admin:

1. Open **WP AI Translation > Report Bug**.
2. Select the problem type.
3. Describe what happened.
4. Add steps to reproduce the issue.
5. Optionally attach the redacted technical log.

Useful details for a bug report:

- WordPress version.
- PHP version.
- WooCommerce version, if WooCommerce is active.
- Active theme.
- Active plugins related to caching, SEO, page builders, and WooCommerce.
- Source language and target languages.
- Selected translation provider.
- Exact page URL where the issue happens.
- Expected result and actual result.

## Feature Requests And Feedback

Use **WP AI Translation > Feedback** for:

- Feature requests.
- Translation quality notes.
- WooCommerce compatibility feedback.
- Elementor widget feedback.
- UI/UX suggestions.
- Public Beta testing notes.

## Public Beta Expectations

During Public Beta, the goal is to improve stability, compatibility, documentation, and setup flow before a production release.

The current beta may still have edge cases with:

- Hosting file permissions.
- WordPress upload updates on some servers.
- Cache plugins.
- Page builders.
- WooCommerce permalink configurations.
- Provider quota and billing limits.

## API Provider Support

WP AI Translate can send translation requests to external providers. API errors are usually controlled by the provider account, region, quota, billing status, model access, or API key restrictions.

Common examples:

- `429`: quota or rate limit reached.
- `403`: API key restriction, billing issue, model access issue, or unsupported region.
- `503`: temporary provider overload.

For production sites, use conservative batch sizes and provider-side billing limits.

## Security And Private Data

Do not send API keys, passwords, cookies, customer records, billing details, or full database exports by email.

If you attach logs from the plugin, WP AI Translate attempts to redact sensitive values, but you should still review the content before sending it.

