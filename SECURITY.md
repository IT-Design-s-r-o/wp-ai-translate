# Security Policy

## Reporting a vulnerability

Please report security issues privately by email:

info@itdesign.biz

Include:

- A short description of the issue.
- Steps to reproduce.
- Affected plugin version.
- WordPress and PHP versions.
- Any relevant logs with secrets redacted.

Please do not publicly disclose a vulnerability before we have had a reasonable opportunity to investigate and prepare a fix.

## Sensitive data

WPAIT Multilingual AI Translate masks provider API keys in the debugger and redacts sensitive values from exported diagnostic logs. Do not send API keys, passwords, cookies, billing data, shipping data, or customer personal data in support messages.

## External services

The plugin sends site text to external translation services only after a site administrator configures a provider API key and starts a provider action, queue run, background translation, or frontend auto-translation request.

