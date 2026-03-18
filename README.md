# laravel-email-platform

> A multi-tenant Laravel 12 API backend for contact form ingestion, safe recipient routing, webhook intake, and admin-managed user provisioning.

[![Tests](https://github.com/your-user/laravel-email-platform/actions/workflows/tests.yml/badge.svg)](https://github.com/your-user/laravel-email-platform/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net)

---

## What it does

Websites and apps submit their contact forms to this platform via a simple HTTP API.
The platform:

1. Resolves which **site** (tenant property) the submission belongs to
2. Routes delivery to that site's fixed **notification inbox** — callers cannot choose arbitrary recipients
3. Persists a full **audit trail** (status lifecycle + event log per message)
4. Dispatches a queued job to render a template and send the email
5. Exposes an **admin panel** where administrators manage users

---

## Key features

| Area | Status |
|---|---|
| Multi-tenant multi-site support | ✅ |
| Locked recipient routing (`notification_email`) | ✅ |
| Contact form API (`POST /api/contact`) | ✅ |
| Webhook intake (`POST /api/webhook/contact-form`) | ✅ |
| Queue-based async email processing | ✅ |
| Full message audit log (events per message) | ✅ |
| Blade + DB template resolution | ✅ |
| RBAC (`admin` / `user` roles) | ✅ |
| Admin-only user management UI | ✅ |
| Public signup disabled | ✅ |
| CLI first-user bootstrap command | ✅ |
| Rate limiting on contact endpoint | ✅ |
| API key / HMAC auth (wired, not enforced yet) | ⚠️ draft |
| Email delivery (uncomment when mailer configured) | ⚠️ draft |
| File attachment upload | ⚠️ stub |

---

## Quick start

```bash
# 1. Install dependencies
composer install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate

# 4. Create first admin user
php artisan platform:bootstrap-user

# 5. Run server
php artisan serve
```

Open `http://localhost:8000/login` for the admin panel.  
Open `http://localhost:8000/api/health` to verify the API.

---

## API endpoints

```
GET  /api/health                    → { "status": "ok" }
GET  /api/version                   → { "app": "...", "laravel": "..." }

POST /api/contact                   → 202 accepted (throttled 30/min)
POST /api/webhook/contact-form      → 202 accepted
```

Both contact endpoints accept `{ name, email, message, subject?, site_key?, file_url? }`.  
Delivery is locked to the resolved site's `notification_email` — never to a caller-supplied address.

See [docs/API.md](docs/API.md) for full request/response examples.

---

## Admin panel

```
GET  /login
POST /login
POST /logout

GET  /admin/users           (admin only)
GET  /admin/users/create    (admin only)
POST /admin/users           (admin only)
```

---

## Demo use cases

Seed three realistic demo sites (freelancer portfolio, agency hub, SaaS webhook intake):

```bash
php artisan migrate
php artisan db:seed
```

See [docs/DEMO_USE_CASES.md](docs/DEMO_USE_CASES.md) for ready-to-run `curl` examples.

---

## Running tests

```bash
php artisan test
```

---

## Documentation

| Doc | Purpose |
|---|---|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | System design, components, data flow |
| [docs/API.md](docs/API.md) | Endpoints, request/response, examples |
| [docs/SETUP.md](docs/SETUP.md) | Installation, config, deployment |
| [docs/TESTING.md](docs/TESTING.md) | Test strategy and writing guide |
| [docs/DEMO_USE_CASES.md](docs/DEMO_USE_CASES.md) | Demo scenarios and sample requests |
| [docs/webhook-signature-contract.md](docs/webhook-signature-contract.md) | Webhook HMAC spec |

---

## Tech stack

- **PHP 8.2+** / **Laravel 12**
- **SQLite** (dev) / **MySQL or PostgreSQL** (production)
- **Redis** (queue, recommended for production)
- **Azure Blob / S3 / local** for file attachments

---

## License

MIT — see [LICENSE](LICENSE).
