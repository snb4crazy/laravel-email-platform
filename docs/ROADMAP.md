# Project Roadmap & TODO

## Documentation ✅ COMPLETE

- [x] Architecture Guide (`docs/ARCHITECTURE.md`)
- [x] API Reference (`docs/API.md`)
- [x] Setup Guide (`docs/SETUP.md`)
- [x] Testing Guide (`docs/TESTING.md`)
- [x] Updated README with documentation links
- [x] Webhook signature contract (`docs/webhook-signature-contract.md`) - existing
- [x] Request contracts (`docs/request-contracts-draft.md`) - existing

## Current Implementation Status

### Phase 1: Core Infrastructure ✅
- [x] Laravel 12 project setup
- [x] Database schema (Users, Sites, SiteCredentials, MailMessages, MailMessageEvents, MailTemplates)
- [x] API routes (`/api/health`, `/api/version`, `/api/contact`, `/api/webhook/contact-form`)
- [x] Request validation (ContactSubmissionRequest, WebhookContactRequest)
- [x] Controllers (ContactSubmissionController)
- [x] Jobs (SendMailJob)
- [x] Models (User, Site, SiteCredential, MailMessage, MailMessageEvent, MailTemplate)
- [x] Services (SiteResolver, MailTemplateResolver, MessageAttachmentStorageService - stub)
- [x] Middleware (DraftContactAuthMiddleware, DraftWebhookSignatureMiddleware)
- [x] Enums (SiteAuthMode, CaptchaProvider, CredentialType)
- [x] Configuration files (draft_auth.php, azure_blob.php, multisite.php)

### Phase 2: Authentication Implementation (NEXT)
- [ ] API Key Validation
  - [ ] DraftContactAuthMiddleware: Enforce API key in X-Api-Key header
  - [ ] Verify key exists in SiteCredential table
  - [ ] Check if credential is active
  - [ ] Add rate limiting per API key
  - [ ] Tests: MiddlewareTest.php

- [ ] Webhook Signature Verification
  - [ ] DraftWebhookSignatureMiddleware: Verify X-Signature HMAC
  - [ ] Extract secret from SiteCredential by X-Key-Id
  - [ ] Verify X-Timestamp within TTL (default 300s)
  - [ ] Check X-Nonce for replay protection (Redis/DB store)
  - [ ] Constant-time hash comparison
  - [ ] Tests: WebhookSignatureTest.php

- [ ] Captcha Verification
  - [ ] Implement reCAPTCHA v3 verification
  - [ ] Implement Cloudflare Turnstile verification
  - [ ] Add to DraftContactAuthMiddleware
  - [ ] Configurable threshold scores
  - [ ] Tests: CaptchaVerificationTest.php

### Phase 3: Email Delivery (CRITICAL)
- [ ] Uncomment `Mail::send()` in SendMailJob::handle()
- [ ] Configure MAIL_MAILER in .env (Postmark recommended for production)
- [ ] Test with each provider (Postmark, Sendgrid, AWS SES, Mailgun)
- [ ] Add retry logic with exponential backoff
- [ ] Implement delivery status tracking from providers
- [ ] Add bounce/complaint handling
- [ ] Tests: SendMailJobTest.php

### Phase 4: Testing ⚠️ IN PROGRESS
Current test status: Some tests fail due to routing configuration in test environment

- [ ] Fix routing in tests (Laravel 12 test environment issue)
- [x] ApiRoutesTest.php - defined, not passing due to routing issue
- [ ] ContactSubmissionTest.php - feature tests for full workflow
- [ ] SendMailJobTest.php - job execution and database recording
- [ ] TemplateResolutionTest.php - template resolver logic
- [ ] SiteResolverTest.php - site matching logic
- [ ] Middleware tests - auth and signature verification
- [ ] Model tests - relationships and scopes
- [ ] Database factory setup for test data generation
- [ ] Test coverage: Target 80%+

### Phase 5: File Attachment Handling
- [ ] Wire MessageAttachmentStorageService
- [ ] Configure Azure Blob Storage connection
- [ ] Add S3 support (alternative)
- [ ] Add local file storage support
- [ ] File size validation
- [ ] File type validation (whitelist)
- [ ] Virus scanning integration
- [ ] Tests: AttachmentStorageTest.php

### Phase 6: Multi-Site Management API (OPTIONAL)
- [ ] Create Site CRUD endpoints
  - [ ] POST /api/admin/sites (create)
  - [ ] GET /api/admin/sites (list)
  - [ ] GET /api/admin/sites/{id} (read)
  - [ ] PUT /api/admin/sites/{id} (update)
  - [ ] DELETE /api/admin/sites/{id} (soft delete)

- [ ] Create SiteCredential CRUD endpoints
  - [ ] POST /api/admin/sites/{id}/credentials
  - [ ] GET /api/admin/sites/{id}/credentials
  - [ ] DELETE /api/admin/sites/{id}/credentials/{credId}

- [ ] Create MailTemplate management
  - [ ] POST /api/admin/templates
  - [ ] PUT /api/admin/templates/{id}
  - [ ] DELETE /api/admin/templates/{id}

- [ ] Authentication policy
  - [ ] Only tenant can manage own sites
  - [ ] Admin can manage all

### Phase 7: Monitoring & Analytics
- [ ] Dashboard endpoints
  - [ ] GET /api/admin/stats (message counts, delivery rates)
  - [ ] GET /api/admin/messages (list with filters)
  - [ ] GET /api/admin/messages/{id} (view details + events)

- [ ] Metrics tracking
  - [ ] Messages per site
  - [ ] Delivery success rate
  - [ ] Average delivery time
  - [ ] Spam rate

- [ ] Alerts
  - [ ] High failure rate alert
  - [ ] Rate limit exceeded alert
  - [ ] Bounce/complaint alert

### Phase 8: Advanced Features
- [ ] Email template versioning
  - [ ] Keep history of template changes
  - [ ] Rollback capability
  - [ ] A/B testing support

- [ ] Dynamic variable substitution
  - [ ] {{name}}, {{email}}, {{custom_field}} support
  - [ ] Conditional blocks in templates
  - [ ] Loop support for array fields

- [ ] Spam detection
  - [ ] Content-based scoring
  - [ ] Email reputation checks
  - [ ] Mark/unmark spam endpoint

- [ ] OpenTrack/webhook notifications
  - [ ] Open tracking (pixel)
  - [ ] Click tracking
  - [ ] Bounce notifications
  - [ ] Complaint notifications

- [ ] Message search & filtering
  - [ ] Full-text search
  - [ ] Filter by date, status, source
  - [ ] Filter by site, tenant
  - [ ] Export to CSV

## Configuration TODO Items

### Environment Variables (Add to .env)
```env
# Mail Provider Setup (CRITICAL)
MAIL_MAILER=postmark              # Choose provider
POSTMARK_API_KEY=xxxxx            # Or SENDGRID_API_KEY, etc.
MAIL_FROM_ADDRESS=noreply@domain
MAIL_FROM_NAME="Your Company"

# Queue Setup (CRITICAL)
QUEUE_CONNECTION=redis            # Switch from sync
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Azure Blob Storage (If using files)
AZURE_STORAGE_ACCOUNT=
AZURE_STORAGE_KEY=
AZURE_CONTAINER=attachments

# Draft Auth Enforcement (When ready)
DRAFT_AUTH_ENFORCE=true
DRAFT_CONTACT_REQUIRE_API_KEY=true
DRAFT_CONTACT_CAPTCHA_ENABLED=true
DRAFT_CONTACT_CAPTCHA_PROVIDER=turnstile
DRAFT_CONTACT_CAPTCHA_SECRET=xxxxx

DRAFT_WEBHOOK_REQUIRE_SIGNATURE=true
```

## Code Cleanup TODO

### Remove Draft/TODO Comments
As features are implemented, remove these markers:
- [ ] DraftContactAuthMiddleware - remove "TODO" comments
- [ ] DraftWebhookSignatureMiddleware - remove "TODO" comments
- [ ] SendMailJob::handle() - uncomment Mail::send(), remove TODO
- [ ] Various config files - remove enforce/todo comments

### Code Quality
- [ ] Add strict typing everywhere
- [ ] Add PHPDoc comments on all public methods
- [ ] Follow PSR-12 coding standard
- [ ] Add readonly properties where appropriate
- [ ] Use null-safe operators

## Deployment TODO

### Before Production
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Configure real database (MySQL/PostgreSQL)
- [ ] Configure real mail provider (Postmark/Sendgrid)
- [ ] Configure Redis for queue and cache
- [ ] Set up HTTPS
- [ ] Configure CORS if needed
- [ ] Set up monitoring/logging (Sentry, DataDog)
- [ ] Set up backup strategy
- [ ] Run all tests with coverage
- [ ] Performance test with load
- [ ] Security audit (OWASP, dependency check)

### Production Checklist
- [ ] Database migrations run
- [ ] Supervisor/systemd for queue worker configured
- [ ] Log rotation configured
- [ ] Cron job for scheduled tasks (if any)
- [ ] Error monitoring service (Sentry)
- [ ] Uptime monitoring
- [ ] Rate limiting configured
- [ ] Database backups scheduled
- [ ] Security headers configured (CORS, CSP, etc.)

## Known Issues

### Current
1. **Test Routing Issue** - API routes return 404 in test environment
   - Routes are registered (`php artisan route:list` shows them)
   - Routes are loaded in test app container
   - But HTTP requests don't match routes
   - Possible Laravel 12 test framework issue with bootstrapping
   - **Workaround**: Currently limiting test coverage to logic tests
   - **TODO**: Debug and fix test HTTP client routing

### Potential Issues to Watch
1. MySQL 8 default authentication - might need `mysql_native_password`
2. Redis connection in production
3. File upload size limits on web server
4. CORS headers if frontend is separate domain
5. Database query N+1 on message lists
6. Template rendering performance with large messages

## Performance Optimizations (Future)

- [ ] Add query optimization
  - [ ] Index foreign keys
  - [ ] Add indexes on frequently searched fields
  - [ ] Eager load relationships

- [ ] Add caching
  - [ ] Cache site configs
  - [ ] Cache credentials
  - [ ] Cache templates

- [ ] Add pagination
  - [ ] Message list pagination
  - [ ] Event log pagination

- [ ] Database archiving
  - [ ] Move old messages to archive
  - [ ] Purge events after N days
  - [ ] Optimize for large-scale data

## Security Hardening (Future)

- [ ] Encrypt sensitive data
  - [ ] Encrypt API secrets in database
  - [ ] Encrypt email addresses
  - [ ] Encrypt file URLs

- [ ] Add audit logging
  - [ ] Log API access
  - [ ] Log admin actions
  - [ ] Log auth failures

- [ ] Add rate limiting per endpoint
- [ ] Add IP whitelisting support
- [ ] Add request signing verification
- [ ] Regular security updates of dependencies

## Scaling Considerations (Future)

For very high volume:

- [ ] Separate read/write database replicas
- [ ] Implement database sharding by tenant
- [ ] Queue workers scaling (Kubernetes, Lambda)
- [ ] Cache layer (Redis cluster)
- [ ] CDN for file attachments
- [ ] Message batching for delivery
- [ ] Elasticsearch for message search

## Documentation Maintenance

- [ ] Update docs after each feature
- [ ] Keep API.md in sync with code
- [ ] Update ARCHITECTURE.md for major changes
- [ ] Add decision records (ADR) for important choices
- [ ] Maintain FAQ section
- [ ] Add troubleshooting guides as issues arise

## Success Metrics

When complete, the system should:

✅ Accept contact form submissions from 100+ sites
✅ Process messages asynchronously with 99.9% reliability
✅ Deliver emails within 2 seconds average
✅ Support 10,000+ messages/day
✅ Have 80%+ test coverage
✅ Scale horizontally (add queue workers)
✅ Have comprehensive audit trails
✅ Support multi-tenant with data isolation
✅ Integrate with multiple email providers
✅ Track all delivery status changes
✅ Prevent spam and replays
✅ Have clear, up-to-date documentation

---

## Version History

- **v0.1.0** (Current) - Draft implementation, auth not enforced, email not sent
  - Core infrastructure in place
  - Documentation complete
  - Ready for auth implementation

- **v0.2.0** (Next) - Auth enforcement + email delivery
  - Implement API key validation
  - Implement webhook signature verification
  - Uncomment mail delivery code
  - Fix test routing issue
  - Achieve 80%+ test coverage

- **v1.0.0** (Future) - Production ready
  - All features implemented
  - Security hardened
  - Performance optimized
  - Deployed and operational

---

Last updated: March 17, 2026

