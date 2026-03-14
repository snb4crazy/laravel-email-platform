# Request Contracts (Draft / TODO Mode)

These contracts are intentionally permissive for now.
No auth enforcement is active yet.

## POST `/api/contact`

### Body

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "message": "Hello",
  "subject": "Optional",
  "file_url": "https://example.com/file.pdf",

  "site_key": "site_public_key_optional",
  "site_domain": "landing.example.com",
  "site_id": 123,

  "captcha_token": "optional-for-now",
  "api_key": "optional-for-now",
  "request_id": "optional-idempotency-key",
  "meta": { "campaign": "spring" }
}
```

### TODO

- Resolve `site_id` from `site_key` server-side.
- Verify `captcha_token` against provider configured for the site.
- Enforce auth mode per site (`none`, `captcha`, `api_key`, `hmac`, `oauth_token`).

## POST `/api/webhook/contact-form`

### Body

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "message": "Webhook payload",
  "subject": "Optional",
  "file_url": "https://example.com/file.pdf",

  "site_key": "optional",
  "site_id": 456,

  "request_id": "optional",
  "timestamp": 1710000000,
  "nonce": "f05fcd0a-7507-4b8f-8709-0f73b7fbad62",
  "signature": "optional-in-draft-mode",
  "meta": { "source": "partner-api" }
}
```

### Planned headers (not enforced yet)

- `X-Key-Id`
- `X-Timestamp`
- `X-Nonce`
- `X-Content-SHA256`
- `X-Signature`

### TODO

- Verify webhook signature in middleware.
- Enforce replay protection (nonce + TTL store).
- Resolve site and tenant from credential key id.

