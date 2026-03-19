# Resume Backlog (Next Features)

_Last updated: March 18, 2026_

This document is the detailed backlog for continuing development after a pause.

## Current Baseline (Verified)

- Web/API routes exist for:
  - `POST /api/contact`
  - `POST /api/webhook/contact-form`
  - `GET /api/health`
  - `GET /api/version`
- Admin + Portal UI exists (Blade/Tailwind).
- RBAC exists (`admin`, `user`) with admin user management.
- Queue flow exists (`SendMailJob`) and message persistence exists:
  - `mail_messages`
  - `mail_message_events`
- Site and credential domain exists:
  - `sites`
  - `site_credentials`
- Draft auth/signature architecture exists (middleware/config/contracts).
- Tests currently pass in repository state (feature + unit).

---

## Phase Plan

## Phase A - MVP Hardening (Priority: P0)

Goal: make ingestion and delivery trustworthy before adding more product surface.

### Epic A1: Enforce Request Trust Boundaries

- [ ] Enforce auth mode by site (`none`, `api_key`, `hmac`, `oauth_token` draft path)
- [ ] Enforce API key check for contact flow (`X-Api-Key`)
- [ ] Enforce webhook signature check (`X-Signature`, `X-Key-Id`, `X-Timestamp`, `X-Nonce`)
- [ ] Implement replay protection store (Redis preferred, DB fallback)
- [ ] Add clock skew validation window (default 300s)
- [ ] Add per-site + per-credential rate limiting
- [ ] Return consistent error contract for auth failures

Definition of done:
- [ ] Invalid key/signature/nonce/timestamp requests are rejected deterministically
- [ ] Replay attempts are blocked and logged
- [ ] Tests cover positive/negative auth paths
- [ ] `docs/API.md` + `docs/webhook-signature-contract.md` updated with final behavior

### Epic A2: Real Email Delivery

- [ ] Replace logging-only send in `SendMailJob` with provider send path
- [ ] Implement provider abstraction (driver interface + adapters)
- [ ] Add first provider integration (recommend: Postmark or SES)
- [ ] Persist provider IDs and normalize status transitions
- [ ] Add retry/backoff + terminal failure path
- [ ] Add dead-letter handling strategy

Definition of done:
- [ ] Message lifecycle moves through `queued/sending/sent/failed`
- [ ] Provider message ID is saved for sent attempts
- [ ] Retry policy is deterministic and tested
- [ ] Failure reasons are recorded in message events

### Epic A3: Tenant/Site Admin API (Operational)

- [ ] Add API endpoints for site CRUD (tenant scoped)
- [ ] Add credential lifecycle endpoints (create/list/revoke/rotate)
- [ ] Add template CRUD endpoints (tenant scoped)
- [ ] Add ownership policy tests for all endpoints

Definition of done:
- [ ] Non-owner access denied for site/credential/template resources
- [ ] Admin override behavior is explicit and tested
- [ ] API docs include examples and permission notes

### Epic A4: Reliability Test Matrix

- [ ] Cover middleware trust boundaries with feature tests
- [ ] Cover queue + send job success/failure modes
- [ ] Cover template resolver precedence (`tenant -> default -> fallback`)
- [ ] Cover `SiteResolver` for domain/public key/header modes
- [ ] Add regression tests for route protection and role access

Definition of done:
- [ ] CI blocks merges on failing tests
- [ ] Critical paths have deterministic feature tests
- [ ] Test data factories support all new scenarios

---

## Phase B - Beta Readiness (Priority: P1)

Goal: make platform usable for real tenants and operators.

### Epic B1: Portal/Admin Productization

- [ ] Improve tenant portal UX for sites/credentials/templates/messages
- [ ] Add status/actions for credential rotation windows
- [ ] Add admin audit page (who changed what/when)
- [ ] Add role expansion plan (`support`, `analyst` optional)

Definition of done:
- [ ] Primary tenant flows can be completed from UI without manual DB edits
- [ ] Admin actions are auditable
- [ ] Access model documented for each role

### Epic B2: Observability + Alerting

- [ ] Add structured logging with correlation IDs (request -> job -> provider)
- [ ] Add dashboards for queue health and delivery status
- [ ] Add alerts for failure spikes, replay spikes, and queue lag
- [ ] Add operational runbook for alerts

Definition of done:
- [ ] On-call can trace a message end-to-end from one ID
- [ ] Alert thresholds are defined and actionable
- [ ] Runbook exists in docs

### Epic B3: Compliance Foundation

- [ ] Define message/event retention policy
- [ ] Add PII handling strategy (masking/encryption/access constraints)
- [ ] Add export/delete tooling for tenant data requests
- [ ] Add legal metadata fields where needed (consent/source)

Definition of done:
- [ ] Retention is enforceable by configuration
- [ ] PII handling documented and applied in code paths
- [ ] Data export/delete workflows are testable

### Epic B4: Attachments (Azure-first)

- [ ] Implement upload flow using `MessageAttachmentStorageService`
- [ ] Add Azure Blob implementation (and local fallback)
- [ ] Validate file type + file size + storage errors
- [ ] Persist attachment metadata and relation to message

Definition of done:
- [ ] Upload works in local + Azure modes
- [ ] Invalid attachments are rejected with clear contract
- [ ] File URLs/keys are stored safely for later delivery usage

---

## Phase C - Scale Readiness (Priority: P2)

Goal: support higher volume and commercial operation.

### Epic C1: Multi-Provider Routing + Provider Webhooks

- [ ] Add second provider adapter
- [ ] Add provider failover rules
- [ ] Ingest provider webhooks (delivered, bounced, complained)
- [ ] Normalize webhook events into `mail_message_events`

Definition of done:
- [ ] Failover works for provider outage scenarios
- [ ] Delivery events are provider-agnostic in analytics/storage

### Epic C2: Data Scale + Archiving

- [ ] Add indexing review for high-volume filters/search
- [ ] Implement archival strategy for old messages/events
- [ ] Add pagination/performance budgets for heavy screens
- [ ] Evaluate partitioning strategy by time/tenant if needed

Definition of done:
- [ ] Core list endpoints remain performant under load target
- [ ] Archive process does not break analytics/reporting expectations

### Epic C3: Billing + Limits

- [ ] Add usage metering (messages/events/storage)
- [ ] Add plan definitions and quotas
- [ ] Enforce quota checks in ingestion path
- [ ] Add usage export/invoice primitives

Definition of done:
- [ ] Usage metrics are tenant-attributed and auditable
- [ ] Plan limits enforced consistently and visible to tenants

### Epic C4: Analytics Product

- [ ] Add delivery funnel analytics (received/queued/sent/failed/delivered)
- [ ] Add per-site and per-tenant trend reports
- [ ] Add spam/abuse trend analysis
- [ ] Add export endpoints for BI tooling

Definition of done:
- [ ] Dashboard answers tenant operational questions without DB access
- [ ] Analytics definitions are documented and versioned

---

## Cross-Cutting TODOs

### Security

- [ ] Secrets rotation policy and tooling
- [ ] Credential hashing/verification hardening review
- [ ] OWASP API review checklist
- [ ] Dependency/CVE checks in CI gates

### DevOps / Release

- [ ] Production-ready queue worker supervision
- [ ] Blue/green or rolling deployment strategy
- [ ] Backup + restore drills
- [ ] Staging parity checklist

### DX / Architecture

- [ ] Formalize provider interfaces/contracts
- [ ] Add architecture decision records (ADRs) for major choices
- [ ] Add coding standards page for project conventions

---

## Dependency Order (Do Not Skip)

1. Enforce request trust boundaries
2. Enable real delivery path
3. Complete tenant ownership and auth tests
4. Improve observability before scale features
5. Add billing only after reliable usage metrics

---

## Fast Resume Suggestions (First Week Back)

- [ ] Read `docs/ROADMAP.md` and this backlog
- [ ] Pick one P0 epic only (recommended: Epic A1)
- [ ] Break epic into 2-3 PR-sized tasks
- [ ] Update docs/contracts immediately after each merged step
- [ ] Keep TODO list status current in this file

