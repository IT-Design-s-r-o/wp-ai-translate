# Security Policy

Security matters for **WP AI Translate**, especially because translation workflows can involve API keys, site content, WooCommerce data, and debug logs.

## Supported Versions

WP AI Translate is currently in **Public Beta**.

| Version | Status |
| --- | --- |
| 0.3.x | Supported during Public Beta |
| Older beta builds | Please update before reporting security issues |

## Reporting A Vulnerability

Please report security issues privately by email:

**info@itdesign.biz**

Use the subject:

`Security Report - WP AI Translate`

Please include:

- Plugin version.
- WordPress version.
- PHP version.
- Affected feature or page.
- Clear reproduction steps.
- Expected result and actual result.
- Screenshots only if they do not expose private data.

Please do not publicly disclose a vulnerability before we have had time to investigate and prepare a fix.

## What Not To Send

Do not send:

- API keys.
- WordPress admin passwords.
- Hosting passwords.
- Cookies or session tokens.
- Full database dumps.
- WooCommerce customer records.
- Billing or shipping data.
- Private access tokens.

If a log is needed, use the plugin's redacted debug export and review it before sending.

## Security Scope

Security reports can include:

- Missing capability checks.
- Missing nonce checks.
- Unsafe AJAX endpoints.
- Unsafe file access.
- Unsafe debug log exposure.
- Stored or reflected XSS.
- SQL injection.
- Sensitive data leakage.
- Broken access control.

## Out Of Scope

The following are usually outside plugin security scope:

- Provider-side API quota errors.
- Provider-side model availability.
- Hosting file permission problems not caused by the plugin.
- Issues caused by leaked API keys outside WP AI Translate.
- Browser extensions or malware on the administrator's computer.

## Handling Debug Logs

WP AI Translate includes safe diagnostic tools for Public Beta testing. Before logs are sent, the plugin attempts to redact:

- API keys.
- Tokens.
- Passwords.
- Cookies.
- Emails.
- Phone numbers.
- WooCommerce customer data.
- Billing and shipping data.

The log should keep non-sensitive technical details such as:

- HTTP status.
- Provider name.
- Language route.
- Batch size.
- Queue status.
- Timestamps.
- Plugin, WordPress, PHP, and WooCommerce versions.

Always review exported logs before sharing them.

