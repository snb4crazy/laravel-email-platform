# Webhook Prep (Minimal Do-Now)

This page is for preparing webhook integration **without enabling enforcement yet**.

## Goal

Keep the app usable now while preparing data, config, and docs so webhook auth can be turned on later with low risk.

## Current Mode

- `POST /api/webhook/contact-form` works in draft mode.
- `draft.webhook.signature` middleware is scaffolded but not enforcing signature yet.
- HMAC credentials can already be created per site in portal.

## Minimal Do-Now Checklist

1. Keep enforcement off.
2. Create per-site HMAC credentials in portal.
3. Save `key_id` in your integration notes.
4. Keep webhook header contract stable in docs.
5. Add one smoke test request in Postman/cURL for each environment.

## Environment Settings (keep draft)

In `.env` (or environment variables), keep:

```dotenv
DRAFT_AUTH_ENFORCE=false
DRAFT_WEBHOOK_REQUIRE_SIGNATURE=false
DRAFT_WEBHOOK_SIGNATURE_ALGO=sha256
DRAFT_WEBHOOK_ALLOWED_CLOCK_SKEW_SECONDS=300
DRAFT_WEBHOOK_HEADER_KEY_ID=X-Key-Id
DRAFT_WEBHOOK_HEADER_TIMESTAMP=X-Timestamp
DRAFT_WEBHOOK_HEADER_NONCE=X-Nonce
DRAFT_WEBHOOK_HEADER_CONTENT_SHA256=X-Content-SHA256
DRAFT_WEBHOOK_HEADER_SIGNATURE=X-Signature
```

## Per-Site Portal Steps

1. Open `Portal -> My Sites -> {Site} -> Credentials`.
2. Create credential type: `hmac`.
3. Store generated values in your password manager:
   - `key_id` (safe identifier)
   - secret (sensitive)
4. Open `Portal -> My Sites -> {Site} -> API Integration` and use webhook example template.

## Suggested Team Convention (now)

- Use one `hmac` credential per environment (dev/stage/prod).
- Rotate secrets on schedule (for example, every 90 days).
- Never put webhook secrets into frontend code.
- Keep replay window policy documented (`300s` default).

## Not Implemented Yet (intentional)

- Signature verification (`X-Signature` HMAC check)
- Timestamp skew reject logic
- Nonce replay storage and reject logic
- Canonical payload hashing (`X-Content-SHA256`) enforcement

## Go-Live Checklist (when ready)

1. Implement and test middleware checks in `app/Http/Middleware/DraftWebhookSignatureMiddleware.php`.
2. Add nonce replay storage (Redis preferred, DB fallback).
3. Add unit tests for signature/timestamp/nonce failures.
4. Enable in staging first:
   - `DRAFT_WEBHOOK_REQUIRE_SIGNATURE=true`
   - Keep `DRAFT_AUTH_ENFORCE=true` only after tests pass.
5. Promote to production after real sender verification.

## Related Docs

- [API.md](API.md)
- [webhook-signature-contract.md](webhook-signature-contract.md)
- [SETUP.md](SETUP.md)

