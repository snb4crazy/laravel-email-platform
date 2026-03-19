# Project Roadmap

_Last updated: March 18, 2026_

This is the high-level roadmap. For the detailed task checklist, see `docs/TODO_BACKLOG.md`.

## Current State Snapshot

### Implemented

- Core API endpoints: health/version/contact/webhook.
- Queue pipeline with `SendMailJob` and message/event persistence.
- RBAC baseline (`admin`, `user`) and admin user management.
- Site/template/credential domain models and portal/admin UI baseline.
- Draft auth/signature architecture and contracts are present.
- Tests are present and passing in current repository state.

### Not Enforced Yet (Intentional Draft Areas)

- Full API key enforcement on web contact endpoint.
- Full webhook signature replay-safe enforcement in production mode.
- Real provider send path (currently still designed for staged rollout).
- Attachment upload flow and delivery integration.

---

## Delivery Phases

## Phase A - MVP Hardening (P0)

Focus: trust boundaries + delivery correctness.

- Enforce request authentication and webhook signature verification.
- Enable real email delivery via provider adapter.
- Finalize tenant/site operational API controls.
- Expand reliability-first test coverage for critical paths.
- Add baseline operational observability and alerting.

Exit criteria:
- Auth/signature can block invalid requests deterministically.
- Message lifecycle and retries are reliable and observable.
- Ownership and permission boundaries are fully tested.

## Phase B - Beta Readiness (P1)

Focus: tenant usability + operator safety.

- Productize portal/admin flows for site, credentials, templates, and messages.
- Add auditability and role refinement.
- Add compliance baseline (retention, PII handling, export/delete flows).
- Implement attachment storage path (Azure-first with fallback).

Exit criteria:
- Tenants can self-serve key workflows.
- Operators can monitor and troubleshoot quickly.
- Data handling controls are documented and testable.

## Phase C - Scale Readiness (P2)

Focus: volume, reliability, monetization.

- Add multi-provider routing/failover and provider webhook ingestion.
- Add data lifecycle/archival/performance optimizations.
- Add billing/quotas based on verified usage metrics.
- Add advanced analytics for tenant and admin insights.

Exit criteria:
- Platform supports higher load without operational fragility.
- Usage and billing data are reliable and auditable.
- Analytics are useful for product and customer operations.

---

## Priority Rules

1. Security/trust boundary work precedes all product expansion.
2. Delivery correctness precedes analytics and billing.
3. Tenant authorization must be tested before opening management APIs.
4. Observability must be in place before scale optimization work.

---

## Planning Links

- Detailed backlog: `docs/TODO_BACKLOG.md`
- API contracts: `docs/API.md`
- Request contracts draft: `docs/request-contracts-draft.md`
- Webhook signature contract: `docs/webhook-signature-contract.md`
- Architecture notes: `docs/ARCHITECTURE.md`
