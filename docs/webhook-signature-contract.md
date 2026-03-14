# Webhook Signature Contract (Draft)

This document defines the planned signature contract for `POST /api/webhook/contact-form`.

Status: draft only. Middleware currently runs in pass-through mode.

## Planned Required Headers

- `X-Key-Id`
- `X-Timestamp` (Unix seconds)
- `X-Nonce` (UUID)
- `X-Content-SHA256` (hex digest of raw request body)
- `X-Signature` (hex HMAC digest)

## Planned Canonical String

```text
{method}\n
{path}\n
{timestamp}\n
{nonce}\n
{content_sha256}
```

Example values:

- `method`: `POST`
- `path`: `/api/webhook/contact-form`
- `timestamp`: `1710000000`
- `nonce`: `f05fcd0a-7507-4b8f-8709-0f73b7fbad62`
- `content_sha256`: SHA-256 digest of raw JSON body

## Planned Verification Steps

1. Resolve API secret from `X-Key-Id`.
2. Validate clock skew using `X-Timestamp`.
3. Reject replay using `X-Nonce` (short TTL store).
4. Recompute body hash from raw payload.
5. Recompute signature with `hash_hmac('sha256', canonical_string, secret)`.
6. Compare signatures in constant time.

## Planned Failure Responses

- `401 Unauthorized` for missing/invalid signature data.
- `409 Conflict` for replayed nonce.
- `422 Unprocessable Entity` for malformed payload.

## TODO Implementation Notes

- Add `api_credentials` table (tenant-scoped keys).
- Add nonce replay store (`redis` preferred, DB fallback).
- Add rotation support (`active`, `revoked`, overlap window).
- Add idempotency key support for webhook retries.

