# Email Platform Architecture

## Overview

This is a Laravel 12 email sending/ingestion platform designed to serve as a backend for single-page applications (React, Vue, etc.). It accepts contact form submissions from websites and processes them through a queue-based job system.

**Current Status**: Draft mode - all auth enforcement is disabled with TODO markers for future implementation.

## High-Level Flow

```
HTTP Request (contact form) 
    → API Route (/api/contact or /api/webhook/contact-form)
    → Middleware (resolve site context, TODO: enforce auth)
    → Controller (validate, dispatch job)
    → SendMailJob (queue job)
    → Save to database (MailMessage + MailMessageEvent)
    → Render template
    → Send email (TODO: uncomment when MAIL_MAILER configured)
    → Record success/failure event
```

## Core Components

### 1. Models

#### `User`
- Represents a tenant/account owner
- Has many `Site`, `MailMessage`, and `MailTemplate` records
- Email platform is multi-tenant with `tenant_id` foreign keys

#### `Site`
- Represents a website/property that submits contact forms
- Has `domain`, `public_key`, `auth_mode`, `captcha_provider`
- Has many `SiteCredential` and `MailMessage` records
- Supports multiple auth modes: API key, OAuth, HMAC signature, captcha

#### `SiteCredential`
- API keys, webhooks keys, OAuth tokens for a site
- Tracks `key_id`, `secret`, `is_active`
- Used for both form submissions and webhook intake

#### `MailMessage`
- Core audit/logging model for all incoming contact messages
- Tracks: `source` (web/webhook), `to_*`/`from_*`, `subject`, `body_text`/`body_html`
- Status lifecycle: `received` → `queued` → `sending` → `sent` / `failed`
- Has many `MailMessageEvent` for detailed logging
- Optional file attachment: `file_url` (e.g., Azure Blob)
- Anti-spam fields: `is_spam`, `spam_reported_at`

#### `MailMessageEvent`
- Immutable audit log of each message's state changes
- Types: `queued`, `sending`, `sent`, `delivered`, `failed`
- Includes `payload` (error details on failure)
- Timestamp: `occurred_at`

#### `MailTemplate`
- HTML/text email templates stored in database
- Scoped to tenant (`tenant_id`) or global (NULL)
- Event-based: `contact_form`, `webhook_contact`, etc.
- Blade templating support with variable substitution

### 2. Controllers

#### `ContactSubmissionController`

**`store()` - POST /api/contact**
- Validates `ContactSubmissionRequest`
- Resolves site context via middleware
- Dispatches `SendMailJob` with type=`web`
- Returns 202 "Contact request received."

**`webhook()` - POST /api/webhook/contact-form**
- Validates `WebhookContactRequest`
- Resolves site context via middleware
- Dispatches `SendMailJob` with type=`webhook`
- Returns 202 "Contact request received."

### 3. Jobs

#### `SendMailJob`
- Async queue job that processes a contact message
- **Responsibilities**:
  1. Create `MailMessage` record (status=`queued`)
  2. Record `MailMessageEvent::queued`
  3. Update status to `sending`, record event
  4. Resolve email template (blade → DB global → DB tenant)
  5. Log template resolution
  6. Send email via `Mail::send()` (currently disabled - TODO)
  7. Update status to `sent`, record event
  8. On failure: update to `failed`, record with error details

**Constructor Parameters**:
```php
- type: 'web' | 'webhook'
- name, email, message, subject (nullable)
- fileUrl (nullable)
- ip, userAgent (nullable)
- tenantId, siteId (nullable - for unresolved requests)
```

### 4. Middleware

#### `DraftContactAuthMiddleware`
- Routes: `POST /api/contact`
- **Current**: Resolves site context only (TODO: enforce auth)
- Uses `SiteResolver` to match request to site/tenant
- Sets `request->attributes['resolved_site']` for downstream use
- **Future**: Verify API key, captcha token, origin

#### `DraftWebhookSignatureMiddleware`
- Routes: `POST /api/webhook/contact-form`
- **Current**: Resolves site context only (TODO: enforce signature)
- **Future**: 
  - Verify HMAC X-Signature header
  - Check X-Timestamp for clock skew
  - Verify X-Nonce for replay protection
  - Resolve site from X-Key-Id header

### 5. Services

#### `SiteResolver`
- Matches incoming request to a `Site` + `User` (tenant)
- **Resolution priority** (first match wins):
  1. `site_key` in request body → Site.public_key
  2. `X-Key-Id` header → SiteCredential.key_id
  3. `Origin` or `Referer` domain → Site.domain
  4. **Unresolved** → pass-through (tenantId/siteId = NULL)

- **Returns**: `ResolvedSite` object with:
  - `siteId`, `tenantId`
  - `authMode` (enum: NONE, API_KEY, OAUTH, HMAC)
  - `captchaProvider` (enum: NONE, RECAPTCHA, TURNSTILE)
  - `resolvedVia` (how it was matched)

#### `MailTemplateResolver`
- Resolves best available template for a message event
- **Priority**:
  1. Blade template in `/resources/views` (fastest)
  2. Database template for tenant (custom per tenant)
  3. Database global template (fallback)
- Returns `ResolvedMailTemplate` with template content + metadata

#### `MessageAttachmentStorageService`
- Handles file upload/download from Azure Blob or local storage
- Future: S3, Google Cloud, etc.
- Currently: stub (file_url is nullable, not enforced)

### 6. Request Classes

#### `ContactSubmissionRequest`
- Validates browser/form submissions
- **Required**: name, email, message
- **Optional**: subject, file_url, site_key, site_domain, site_id, captcha_token, api_key, request_id, meta
- **Draft fields** (no validation yet): captcha_token, api_key for future auth

#### `WebhookContactRequest`
- Validates server-to-server submissions
- **Required**: name, email, message
- **Optional**: subject, file_url, site_key, site_id
- **Draft fields**: x_timestamp, x_nonce, x_signature, x_key_id

### 7. Enums

#### `SiteAuthMode`
- `NONE` - Public, no auth needed (for testing/draft)
- `API_KEY` - X-Api-Key header required
- `OAUTH` - Bearer token required
- `HMAC` - X-Signature header + body hash required

#### `CaptchaProvider`
- `NONE` - No captcha
- `RECAPTCHA` - Google reCAPTCHA v3
- `TURNSTILE` - Cloudflare Turnstile

#### `CredentialType`
- `API_KEY` - Static API key credential
- `OAUTH_TOKEN` - OAuth2 access token
- `WEBHOOK_KEY` - HMAC webhook signing key

## Request Flow Diagrams

### Web Form Submission

```
1. Frontend (React/Vue) submits form
   POST /api/contact
   {
     "name": "John Doe",
     "email": "john@example.com",
     "message": "Contact me please",
     "subject": "Inquiry",
     "site_key": "pk_abc123"  // Optional
   }

2. DraftContactAuthMiddleware
   - Resolve site by site_key → Site ID
   - Set request.attributes.resolved_site

3. ContactSubmissionController::store()
   - Validate request
   - Dispatch SendMailJob (type='web')

4. SendMailJob (async)
   - Create MailMessage (status=queued)
   - Record MailMessageEvent::queued
   - Update status=sending
   - Resolve template
   - Send email (mocked for now)
   - Update status=sent
   - Record MailMessageEvent::sent

5. Database State
   - MailMessage: 1 record with all submission data
   - MailMessageEvent: 3 events (queued, sending, sent)
```

### Webhook Intake

```
1. External service (e.g., form builder) submits
   POST /api/webhook/contact-form
   Headers:
     X-Key-Id: cred_abc123
     X-Timestamp: 2026-03-17T12:00:00Z
     X-Nonce: nonce_xyz
     X-Signature: hmac-sha256-hash
   Body:
     {
       "name": "Jane Smith",
       "email": "jane@example.com",
       "message": "Webhook test",
       "site_id": 42  // Can specify site directly
     }

2. DraftWebhookSignatureMiddleware
   - Resolve site by X-Key-Id or site_id
   - Set request.attributes.resolved_site
   - (TODO: Verify signature, nonce, timestamp)

3. ContactSubmissionController::webhook()
   - Validate request
   - Dispatch SendMailJob (type='webhook')

4. SendMailJob (async)
   - Similar flow as web form, but type='webhook'
   - Template resolves to webhook_contact variant
```

## Database Schema

### MailMessage Table
```
id, tenant_id, site_id, source (web|webhook), 
from_name, from_email, to_name, to_email, reply_to,
subject, body_text, body_html, file_url,
status (received|queued|sending|sent|delivered|failed|cancelled),
mailer (configured mailer driver used),
provider_message_id (from SMTP provider),
is_spam, spam_reported_at,
ip, user_agent, metadata (JSON),
created_at, updated_at, deleted_at
```

### MailMessageEvent Table
```
id, mail_message_id, type (queued|sending|sent|delivered|failed),
payload (JSON - error details on failure), occurred_at,
created_at, deleted_at
```

### MailTemplate Table
```
id, tenant_id, event_type (contact_form|webhook_contact),
name, subject_template, body_html, body_text,
is_default, is_active,
created_at, updated_at, deleted_at
```

### Site Table
```
id, tenant_id, name, domain, public_key, auth_mode,
captcha_provider, captcha_site_key, captcha_secret,
is_active, metadata (JSON),
created_at, updated_at, deleted_at
```

### SiteCredential Table
```
id, site_id, name, type (api_key|oauth_token|webhook_key),
key_id, secret (encrypted), is_active,
metadata (JSON), last_used_at,
created_at, updated_at, deleted_at
```

## Configuration

### `/config/draft_auth.php`
- Toggle enforcement via `DRAFT_AUTH_ENFORCE`
- API key header name, captcha settings
- Webhook signature algorithm
- Rate limiting per IP/tenant

### `/config/mail.php`
- Mailer driver (sendmail, SMTP, Mailgun, Postmark, etc.)
- From address, from name
- Currently configured as `MAIL_MAILER=array` in tests

### `/config/azure_blob.php`
- Azure connection string
- Container name for attachments
- Currently not wired - file_url is nullable

### `/config/multisite.php`
- Site resolution settings
- Request field names for site matching
- Tenant context provider

## API Routes

### Health Check
```
GET /api/health
→ 200 { "status": "ok" }
```

### Version Info
```
GET /api/version
→ 200 {
    "app": "Email Platform",
    "laravel": "12.54.1"
  }
```

### Contact Form Submission
```
POST /api/contact
Headers: (none required in draft mode)
Body: {
  "name": "string (required, max:255)",
  "email": "email (required)",
  "message": "string (required, max:5000)",
  "subject": "string (optional, max:255)",
  "file_url": "url (optional, max:2048)",
  "site_key": "string (optional, max:128)",
  "site_domain": "string (optional, max:255)",
  "site_id": "integer (optional, exists:sites)",
  "captcha_token": "string (optional, max:4096)",
  "api_key": "string (optional, max:255)",
  "request_id": "string (optional, max:128)",
  "meta": "array (optional)"
}
→ 202 { "message": "Contact request received." }
→ 422 { "errors": { "name": [...], ... } }
```

### Webhook Contact Form
```
POST /api/webhook/contact-form
Headers (draft mode - not enforced):
  X-Key-Id: credential key id
  X-Timestamp: ISO 8601 timestamp
  X-Nonce: unique nonce for this request
  X-Signature: HMAC-SHA256 of request body
Body: {
  "name": "string (required)",
  "email": "email (required)",
  "message": "string (required)",
  "subject": "string (optional)",
  "file_url": "url (optional)",
  "site_key": "string (optional)",
  "site_id": "integer (optional)"
}
→ 202 { "message": "Contact request received." }
→ 422 { "errors": { ... } }
```

## Future Enhancements (TODO)

### Authentication & Authorization
- [ ] Implement API key validation in DraftContactAuthMiddleware
- [ ] Implement HMAC signature verification in DraftWebhookSignatureMiddleware
- [ ] Add rate limiting per IP, per API key, per tenant
- [ ] Support OAuth2 for third-party integrations
- [ ] Add captcha verification (reCAPTCHA, Turnstile)

### Email Delivery
- [ ] Uncomment `Mail::send()` in SendMailJob
- [ ] Configure MAIL_MAILER in .env
- [ ] Add retry logic with exponential backoff
- [ ] Track delivery status from SMTP providers

### File Attachments
- [ ] Implement MessageAttachmentStorageService
- [ ] Configure Azure Blob connection
- [ ] Add file size/type validation
- [ ] Add virus scanning pre-upload

### Templates
- [ ] Create UI for database template management
- [ ] Add template versioning
- [ ] Add A/B testing support
- [ ] Support dynamic variable substitution

### Analytics & Monitoring
- [ ] Add dashboard for message metrics
- [ ] Implement spam detection
- [ ] Track open/click rates
- [ ] Alert on delivery failures

### Multi-Site Support
- [ ] Fully implement SiteResolver with database lookups
- [ ] Add site management API
- [ ] Support multiple authentication modes per site
- [ ] Track per-site metrics

## Testing Strategy

See `tests/Feature/ApiRoutesTest.php` for:
- Health endpoint tests
- Version endpoint tests
- Contact submission (valid/invalid)
- Webhook intake (valid/invalid)
- Validation error response tests

See `tests/Unit/` for:
- Model relationship tests
- Service unit tests
- Job logic tests

## Deployment Notes

### Environment Variables
```
APP_NAME=Email Platform
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=email_platform
DB_USERNAME=...
DB_PASSWORD=...

MAIL_MAILER=postmark  # or smtp, sendmail, etc.
POSTMARK_API_KEY=...

DRAFT_AUTH_ENFORCE=false  # Set to true when auth is ready

AZURE_STORAGE_ACCOUNT=...
AZURE_STORAGE_KEY=...
AZURE_CONTAINER=attachments
```

### Queue Configuration
- Default queue driver: sync (for testing)
- Production: redis or database
- Jobs: SendMailJob (async email delivery)

### Logging
- Default: single file in `storage/logs/`
- Configure channels in `config/logging.php`
- All key events logged via MailMessageEvent

## Support & Maintenance

- **Code Status**: Draft/POC - expect changes
- **Auth Status**: Disabled (TODO enforcement)
- **Email Status**: Mocked (uncomment when configured)
- **File Upload**: Not enforced (feature stub)

---

Last updated: March 17, 2026

