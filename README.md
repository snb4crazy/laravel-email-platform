<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Project Overview

This is a Laravel 12 email sending/ingestion platform designed as a backend for single-page applications (React, Vue, etc.). It handles contact form submissions from websites and processes them through a queue-based system.

**Current Status**: Draft mode - all authentication enforcement is disabled with TODO markers for future implementation.

## Quick Start

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Start server
php artisan serve
```

Visit `http://localhost:8000/api/health` to verify the API is running.

## Documentation

### For Developers
- **[Architecture Guide](docs/ARCHITECTURE.md)** - Complete system design, components, database schema, data flow
- **[API Reference](docs/API.md)** - All endpoints with examples (cURL, JavaScript, Python)
- **[Setup Guide](docs/SETUP.md)** - Installation, configuration, Docker setup, troubleshooting
- **[Testing Guide](docs/TESTING.md)** - How to write and run tests, test assertions reference

### For Integration Partners
- **[API Quick Reference](docs/API.md)** - Endpoints, request/response examples
- **[Webhook Signature Contract](docs/webhook-signature-contract.md)** - HMAC signature verification
- **[Request Contracts (Draft)](docs/request-contracts-draft.md)** - Field definitions and validation rules

## Key Features

✅ **Multi-Tenant Support**
- Each user (tenant) can have multiple sites
- Sites have different auth modes and settings
- All data scoped to tenant

✅ **Flexible Authentication (Draft)**
- API Key support (ready for enforcement)
- OAuth token support (ready for enforcement)
- HMAC webhook signature (ready for enforcement)
- Captcha integration (reCAPTCHA, Turnstile - ready)

✅ **Email Processing**
- Async job queue for email delivery
- Template resolution (Blade → DB → Fallback)
- Event-based audit logging
- Provider support (Postmark, Sendgrid, AWS SES, Mailgun)

✅ **Contact Message Storage**
- Complete audit trail of all submissions
- Per-message event log (queued, sending, sent, failed)
- Spam detection fields
- File attachment tracking
- Anti-spam and analytics support

✅ **Two Integration Paths**
- **Web Forms**: POST `/api/contact` for browser-based submissions
- **Webhooks**: POST `/api/webhook/contact-form` for server-to-server

## Core Components

### Models
- **User** - Tenant/account owner (multi-tenant)
- **Site** - Website/property submitting forms
- **SiteCredential** - API keys, webhook keys
- **MailMessage** - Contact message audit record
- **MailMessageEvent** - Event log for each message
- **MailTemplate** - Email templates (database + Blade)

### Controllers
- **ContactSubmissionController** - Handles `/api/contact` and `/api/webhook/contact-form`

### Jobs
- **SendMailJob** - Async processing: create record, render template, send email, log results

### Middleware
- **DraftContactAuthMiddleware** - Site resolution for form submissions (TODO: enforce auth)
- **DraftWebhookSignatureMiddleware** - Site resolution for webhooks (TODO: verify signature)

### Services
- **SiteResolver** - Matches request to site/tenant by key/domain/header
- **MailTemplateResolver** - Picks best available template (Blade → DB → Fallback)
- **MessageAttachmentStorageService** - File upload/download (Azure Blob, local, S3)

## API Endpoints

### Health & Version
```
GET /api/health       → { "status": "ok" }
GET /api/version      → { "app": "Email Platform", "laravel": "12.54.1" }
```

### Contact Form Submission
```
POST /api/contact
Body: { name, email, message, subject?, site_key?, file_url? }
→ 202 { "message": "Contact request received." }
→ 422 { "errors": { ... } }
```

### Webhook Contact Form
```
POST /api/webhook/contact-form
Headers: X-Key-Id, X-Timestamp, X-Nonce, X-Signature
Body: { name, email, message, subject?, site_id? }
→ 202 { "message": "Contact request received." }
→ 422 { "errors": { ... } }
```

See [API.md](docs/API.md) for complete endpoint documentation with examples.

## Database Schema

**MailMessage**: Stores all incoming contact messages with source, recipient, subject, body, status, IP, user-agent.

**MailMessageEvent**: Immutable audit log - records state transitions (queued→sending→sent/failed) with timestamps and error details.

**MailTemplate**: Email templates scoped to tenant or global, Blade-based with variable substitution.

**Site & SiteCredential**: Website configurations, auth modes, API keys for integration.

See [Architecture.md](docs/ARCHITECTURE.md#database-schema) for full schema details.

## Authentication (Draft Mode - Not Enforced)

Currently all authentication is disabled (pass-through). When enforced, supports:

### Contact Form Authentication
- **API Key**: `X-Api-Key` header
- **Captcha**: reCAPTCHA v3 or Cloudflare Turnstile
- **Origin**: Browser origin verification

### Webhook Authentication
- **HMAC Signature**: `X-Signature` header with SHA256
- **Timestamp**: `X-Timestamp` with TTL check
- **Nonce**: `X-Nonce` for replay protection

Enable enforcement: Set `DRAFT_AUTH_ENFORCE=true` in `.env`

See [draft_auth.php](config/draft_auth.php) and [webhook signature contract](docs/webhook-signature-contract.md).

## Configuration

### Mail Setup
```env
# Development: Log to file
MAIL_MAILER=log

# Production: Postmark, Sendgrid, AWS SES, Mailgun
MAIL_MAILER=postmark
POSTMARK_API_KEY=...
```

### Queue Setup
```env
# Development: Sync (immediate execution)
QUEUE_CONNECTION=sync

# Production: Redis or database
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

### Database Setup
```env
# Development: SQLite
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Production: MySQL or PostgreSQL
DB_CONNECTION=mysql
DB_HOST=...
```

See [Setup.md](docs/SETUP.md) for complete configuration guide.

## Running Tests

```bash
# All tests
php artisan test

# Feature tests only
php artisan test tests/Feature

# Unit tests only
php artisan test tests/Unit

# With coverage
php artisan test --coverage
```

See [Testing.md](docs/TESTING.md) for test writing guide and examples.

## Draft Features (TODO - Enforcement Not Active)

All auth checks are currently **disabled with TODO markers** for future implementation:

- ❌ API key verification (implemented, not enforced)
- ❌ HMAC signature verification (schema ready, not enforced)
- ❌ Captcha verification (config ready, not enforced)
- ❌ Email delivery (code commented, ready to uncomment)
- ❌ File attachment upload (stub methods, not implemented)
- ❌ Rate limiting (config ready, not enforced)

Each middleware and service has clear TODO comments showing where enforcement will be added.

## Directory Structure

```
app/
├── Http/Controllers/ContactSubmissionController.php
├── Http/Middleware/DraftContactAuthMiddleware.php
├── Http/Middleware/DraftWebhookSignatureMiddleware.php
├── Http/Requests/ContactSubmissionRequest.php
├── Http/Requests/WebhookContactRequest.php
├── Jobs/SendMailJob.php
├── Models/MailMessage.php, MailMessageEvent.php, MailTemplate.php, Site.php, SiteCredential.php, User.php
├── Services/Mail/MailTemplateResolver.php
├── Services/Site/SiteResolver.php
└── Enums/SiteAuthMode.php, CaptchaProvider.php, CredentialType.php

config/
├── draft_auth.php
├── azure_blob.php
└── multisite.php

database/
├── migrations/
├── factories/
└── seeders/

docs/
├── ARCHITECTURE.md          (Complete system design)
├── API.md                   (API reference with examples)
├── SETUP.md                 (Installation and configuration)
├── TESTING.md               (How to write and run tests)
├── request-contracts-draft.md
└── webhook-signature-contract.md

tests/
├── Feature/ApiRoutesTest.php
├── Unit/ExampleTest.php
└── TestCase.php
```

## Contributing

This is a draft/POC project. Contributions should:

1. Add tests for new features
2. Update documentation
3. Mark enforcement-ready code with TODO comments
4. Follow Laravel best practices

## Support

- **Questions?** Check the relevant documentation file
- **Bug?** Check `storage/logs/laravel.log`
- **Tests failing?** See [Testing.md](docs/TESTING.md) troubleshooting

## Next Steps

1. **Read Architecture**: Start with [Architecture.md](docs/ARCHITECTURE.md) to understand the system
2. **Setup Development**: Follow [Setup.md](docs/SETUP.md)
3. **Test Integration**: Use [API.md](docs/API.md) to test endpoints
4. **Configure Mail**: Set up actual email driver for production
5. **Enable Auth**: Set `DRAFT_AUTH_ENFORCE=true` when ready
6. **Deploy**: Follow your organization's deployment procedures

## API Draft Auth Scaffold

This project includes draft-only auth middleware for contact ingestion:

- `POST /api/contact` uses `DraftContactAuthMiddleware`
- `POST /api/webhook/contact-form` uses `DraftWebhookSignatureMiddleware`

Both are pass-through for now with clear TODO markers for future enforcement. 

Request contract docs:
- [Request Contracts (Draft)](docs/request-contracts-draft.md)
- [Webhook Signature Contract](docs/webhook-signature-contract.md)
