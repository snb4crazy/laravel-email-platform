# API Quick Reference

## Base URL
```
http://localhost:8000/api    (development)
https://api.yourdomain.com   (production)
```

## Authentication (Draft Mode - Not Enforced Yet)

When enforcement is enabled, use one of:
- **API Key**: `X-Api-Key: your_api_key_here`
- **Bearer Token**: `Authorization: Bearer your_token_here`
- **HMAC Signature**: See `docs/webhook-signature-contract.md`

## Endpoints

### 1. Health Check
```
GET /api/health
```

**Response (200 OK):**
```json
{
  "status": "ok"
}
```

**Use Case**: Verify API is running, load balancer health checks

---

### 2. Version Info
```
GET /api/version
```

**Response (200 OK):**
```json
{
  "app": "Email Platform",
  "laravel": "12.54.1"
}
```

**Use Case**: Check API version for compatibility

---

### 3. Contact Form Submission (Web Forms)
```
POST /api/contact
```

**Headers** (optional, draft mode):
```
Content-Type: application/json
X-Api-Key: your_api_key (optional)
X-Request-ID: unique_id (optional)
```

**Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "message": "I would like more information about your services.",
  "subject": "Service Inquiry",
  "site_key": "pk_site123",
  "file_url": "https://cdn.example.com/attachment.pdf",
  "request_id": "req_12345"
}
```

**Required Fields:**
- `name` (string, max 255)
- `email` (valid email)
- `message` (string, max 5000)

**Optional Fields:**
- `subject` (string, max 255)
- `file_url` (valid URL, max 2048)
- `site_key` (string, max 128) - identifies which site this is from
- `request_id` (string, max 128) - for idempotency
- `meta` (object) - custom metadata

**Delivery Behavior:**
- If the request resolves to a known site, delivery is locked to that site's `notification_email`.
- The caller-provided `email` is treated as submitter context (`reply_to`) so owners can respond.
- This prevents callers from choosing arbitrary recipient addresses.

**Rate Limit:**
- `POST /api/contact` is currently throttled at `30 requests/minute` per IP.

**Response (202 Accepted):**
```json
{
  "message": "Contact request received."
}
```

**Response (422 Unprocessable Entity):**
```json
{
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email must be a valid email address."],
    "message": ["The message field is required."]
  }
}
```

**Example cURL:**
```bash
curl -X POST http://localhost:8000/api/contact \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: your_api_key" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "message": "Hello, I am interested in your services"
  }'
```

**Example JavaScript/Fetch:**
```javascript
const response = await fetch('https://api.yourdomain.com/api/contact', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-Api-Key': 'your_api_key'
  },
  body: JSON.stringify({
    name: 'Jane Doe',
    email: 'jane@example.com',
    message: 'Your message here',
    subject: 'Your subject'
  })
});

if (response.ok) {
  console.log('Message received!');
} else {
  console.error('Error:', response.status);
}
```

For seeded demo scenarios and copy/paste requests, see [DEMO_USE_CASES.md](DEMO_USE_CASES.md).

---

### 4. Webhook Contact Form (Server-to-Server)
```
POST /api/webhook/contact-form
```

**Headers** (draft mode - not enforced yet):
```
Content-Type: application/json
X-Key-Id: cred_abc123
X-Timestamp: 2026-03-17T12:00:00Z
X-Nonce: nonce_xyz789
X-Signature: sha256=hmac_digest_here
```

**Request Body:**
```json
{
  "name": "John Smith",
  "email": "john@example.com",
  "message": "This submission came from our webhook",
  "subject": "Webhook Test",
  "site_key": "pk_site123",
  "site_id": 42,
  "x_timestamp": "2026-03-17T12:00:00Z",
  "x_nonce": "nonce_xyz789"
}
```

**Required Fields:**
- `name` (string, max 255)
- `email` (valid email)
- `message` (string, max 5000)

**Optional Fields:**
- `subject` (string, max 255)
- `file_url` (valid URL)
- `site_key` (string) - alternative to site_id
- `site_id` (integer) - alternative to site_key
- `x_timestamp` (ISO 8601) - for replay protection
- `x_nonce` (string) - for replay protection

**Response (202 Accepted):**
```json
{
  "message": "Contact request received."
}
```

**Response (422 Unprocessable Entity):**
```json
{
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email must be a valid email address."],
    "message": ["The message field is required."]
  }
}
```

**Example cURL:**
```bash
TIMESTAMP=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
NONCE=$(openssl rand -hex 16)
PAYLOAD='{"name":"John","email":"john@example.com","message":"Test"}'
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -mac HMAC -macopt key:your_webhook_secret | cut -d' ' -f2)

curl -X POST http://localhost:8000/api/webhook/contact-form \
  -H "Content-Type: application/json" \
  -H "X-Key-Id: cred_abc123" \
  -H "X-Timestamp: $TIMESTAMP" \
  -H "X-Nonce: $NONCE" \
  -H "X-Signature: sha256=$SIGNATURE" \
  -d "$PAYLOAD"
```

---

## Error Handling

### 422 Validation Error
Request is malformed or missing required fields.

```json
{
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Fix**: Check field names, data types, and max lengths

### 401 Unauthorized (When Enforcement Enabled)
Missing or invalid API key / authentication token.

```json
{
  "message": "Unauthorized"
}
```

**Fix**: Verify `X-Api-Key` or `Authorization` header

### 403 Forbidden (When Enforcement Enabled)
Request origin/site not allowed.

```json
{
  "message": "Forbidden"
}
```

**Fix**: Verify site_key matches registered site

### 500 Internal Server Error
Server error occurred.

```json
{
  "message": "Server error",
  "error": "Error details"  // Only in development
}
```

**Check**: Server logs in `storage/logs/laravel.log`

---

## Response Codes Summary

| Code | Meaning | When |
|------|---------|------|
| 200 | OK | Health/version endpoints |
| 202 | Accepted | Contact submission successful, queued for processing |
| 400 | Bad Request | Malformed request JSON |
| 401 | Unauthorized | Invalid/missing API key (enforcement enabled) |
| 403 | Forbidden | Site/tenant not allowed (enforcement enabled) |
| 404 | Not Found | Endpoint doesn't exist |
| 422 | Unprocessable Entity | Validation error |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

---

## Data Types

### Email Message Object
Returned in database but not in API responses (for audit):

```json
{
  "id": 1,
  "tenant_id": 42,
  "site_id": 100,
  "source": "web",
  "from_name": "Email Platform",
  "from_email": "noreply@example.com",
  "to_name": "Jane Doe",
  "to_email": "jane@example.com",
  "reply_to": null,
  "subject": "Service Inquiry",
  "body_text": "I would like more information...",
  "body_html": "<p>I would like more information...</p>",
  "file_url": "https://cdn.example.com/file.pdf",
  "status": "sent",
  "mailer": "postmark",
  "provider_message_id": "abc123def456",
  "is_spam": false,
  "spam_reported_at": null,
  "ip": "192.168.1.100",
  "user_agent": "Mozilla/5.0...",
  "metadata": {
    "request_id": "req_12345"
  },
  "created_at": "2026-03-17T12:00:00Z",
  "updated_at": "2026-03-17T12:01:00Z"
}
```

---

## Rate Limiting (When Enforced)

Default limits (configurable):
- 60 requests per minute per IP
- 100 requests per minute per API key
- 10 submissions per hour per email address

**Headers on limited response:**
```
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1647503700
```

---

## Idempotency

Use `request_id` in body to ensure duplicate requests return same result:

```json
{
  "name": "Jane",
  "email": "jane@example.com",
  "message": "Test",
  "request_id": "req_unique_id_12345"
}
```

If sent twice with same `request_id`, second request will return same result without duplicating message.

---

## Testing the API

### Using Postman
1. Create new request
2. Method: POST
3. URL: `http://localhost:8000/api/contact`
4. Headers: `Content-Type: application/json`
5. Body (raw JSON):
   ```json
   {
     "name": "Test User",
     "email": "test@example.com",
     "message": "This is a test"
   }
   ```
6. Send

### Using REST Client (VS Code Extension)
Create `test.http` file:
```http
POST http://localhost:8000/api/contact HTTP/1.1
Content-Type: application/json

{
  "name": "Test User",
  "email": "test@example.com",
  "message": "This is a test"
}
```

### Using Thunder Client (Chrome Extension)
Similar to Postman, set method, URL, headers, and body.

---

## Webhook Integration Example

```python
# Example: Send to Email Platform from Python
import requests
import json
from datetime import datetime
import hmac
import hashlib

url = "https://api.yourdomain.com/api/webhook/contact-form"
webhook_secret = "your_webhook_secret_key"

data = {
    "name": "John Doe",
    "email": "john@example.com",
    "message": "Contact me with more info",
    "site_id": 42
}

timestamp = datetime.utcnow().isoformat() + "Z"
nonce = "nonce_" + str(int(datetime.utcnow().timestamp()))
payload = json.dumps(data)

signature = hmac.new(
    webhook_secret.encode(),
    payload.encode(),
    hashlib.sha256
).hexdigest()

headers = {
    "Content-Type": "application/json",
    "X-Key-Id": "cred_abc123",
    "X-Timestamp": timestamp,
    "X-Nonce": nonce,
    "X-Signature": f"sha256={signature}"
}

response = requests.post(url, data=payload, headers=headers)
print(response.status_code, response.json())
```

---

## References

- **Full Architecture**: See `docs/ARCHITECTURE.md`
- **Webhook Contracts**: See `docs/webhook-signature-contract.md`
- **Request Contracts**: See `docs/request-contracts-draft.md`
- **Setup Guide**: See `docs/SETUP.md`
- **Testing**: See `docs/TESTING.md`

---

Last updated: March 17, 2026

