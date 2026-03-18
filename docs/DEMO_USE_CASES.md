# Demo Use Cases

This document gives safe, real-world demo scenarios for the base app.

## Why this is safer for public demos

- Contact submissions are routed to each site's fixed `notification_email`.
- The caller-provided `email` is treated as the submitter address (used for reply context), not a free-form recipient.
- `POST /api/contact` is throttled at 30 requests/minute per IP.

## Seed demo data

```bash
php artisan migrate
php artisan db:seed
```

Seeded site keys:
- `demo_portfolio_pk`
- `demo_agency_pk`
- `demo_saas_pk`

## Use Case 1: Freelancer Portfolio Contact Form

- **Scenario**: A personal portfolio site captures inbound leads.
- **Site key**: `demo_portfolio_pk`
- **Delivery inbox**: `freelancer-inbox@example.com`

```bash
curl -s -X POST http://localhost:8000/api/contact \
  -H 'Content-Type: application/json' \
  -d '{
    "site_key": "demo_portfolio_pk",
    "name": "Alice Visitor",
    "email": "alice@example.com",
    "subject": "Project inquiry",
    "message": "Can we discuss a landing page redesign?"
  }'
```

## Use Case 2: Agency Multi-Site Contact Hub

- **Scenario**: Multiple sites route leads into one agency inbox.
- **Site key**: `demo_agency_pk`
- **Delivery inbox**: `agency-sales@example.com`

```bash
curl -s -X POST http://localhost:8000/api/contact \
  -H 'Content-Type: application/json' \
  -d '{
    "site_key": "demo_agency_pk",
    "name": "Bob Founder",
    "email": "bob@startup.test",
    "subject": "Need PPC + SEO",
    "message": "Please share a monthly retainer estimate."
  }'
```

## Use Case 3: SaaS Webhook Intake

- **Scenario**: An external form provider posts leads server-to-server.
- **Header key id**: `demo_webhook_key_01`
- **Delivery inbox**: `saas-growth@example.com`

```bash
curl -s -X POST http://localhost:8000/api/webhook/contact-form \
  -H 'Content-Type: application/json' \
  -H 'X-Key-Id: demo_webhook_key_01' \
  -d '{
    "name": "Charlie Integrator",
    "email": "charlie@partner.test",
    "subject": "Webhook lead",
    "message": "A new signup completed pricing step.",
    "site_key": "demo_saas_pk"
  }'
```

## Verify what happened

```bash
php artisan tinker
```

```php
App\Models\MailMessage::query()
    ->latest('id')
    ->take(5)
    ->get(['id', 'source', 'to_email', 'reply_to', 'status', 'site_id'])
    ->toArray();
```

