# Documentation Index

Welcome to the Email Platform documentation! This guide will help you find what you need.

## Quick Navigation

### I want to...

**...understand how the system works**
→ Start with [ARCHITECTURE.md](ARCHITECTURE.md)
- System overview and data flow
- All components explained
- Database schema
- Request flow diagrams

**...set up the project locally**
→ Read [SETUP.md](SETUP.md)
- Installation steps
- Configuration options
- Database setup
- Mail provider setup
- Docker support
- Troubleshooting

**...integrate with the API**
→ Check [API.md](API.md)
- All endpoints with examples
- Request/response formats
- Error codes
- Code examples (cURL, JavaScript, Python)
- Rate limiting info

**...write tests**
→ See [TESTING.md](TESTING.md)
- How to run tests
- How to write tests
- Test assertions reference
- Debugging tests
- Best practices

**...know what's coming next**
→ Look at [ROADMAP.md](ROADMAP.md)
- Feature roadmap
- TODO items
- Known issues
- Performance optimizations planned
- Success metrics

**...integrate a webhook**
→ Read [webhook-signature-contract.md](webhook-signature-contract.md)
- Signature verification
- Header requirements
- Examples in multiple languages

**...understand request validation**
→ See [request-contracts-draft.md](request-contracts-draft.md)
- All fields and their validation rules
- Optional vs required
- Field constraints

## Documentation Files

### Core Documentation

| File | Purpose | Audience |
|------|---------|----------|
| [ARCHITECTURE.md](ARCHITECTURE.md) | System design, components, database | Developers |
| [API.md](API.md) | API endpoints, examples, integration | Developers, Integration Partners |
| [SETUP.md](SETUP.md) | Installation, configuration, deployment | DevOps, Developers |
| [TESTING.md](TESTING.md) | Testing strategies, how to write tests | QA, Developers |
| [CICD.md](CICD.md) | GitHub Actions CI/CD workflows explained | DevOps, Developers |
| [ROADMAP.md](ROADMAP.md) | Feature roadmap, TODO items, planning | Project Managers, Developers |

### Contract Documentation

| File | Purpose | Audience |
|------|---------|----------|
| [request-contracts-draft.md](request-contracts-draft.md) | Request field validation rules | Integration Partners, Developers |
| [webhook-signature-contract.md](webhook-signature-contract.md) | Webhook signature verification | Integration Partners |

## By Role

### I'm a Developer

1. Start here: [ARCHITECTURE.md](ARCHITECTURE.md) - Understand the system
2. Setup: [SETUP.md](SETUP.md) - Get it running locally
3. API testing: [API.md](API.md) - Test endpoints
4. Testing: [TESTING.md](TESTING.md) - Write and run tests
5. Integration: [webhook-signature-contract.md](webhook-signature-contract.md) - Webhook details

### I'm an Integration Partner

1. Start here: [API.md](API.md) - All endpoints and examples
2. Request details: [request-contracts-draft.md](request-contracts-draft.md) - Field validation
3. Webhooks: [webhook-signature-contract.md](webhook-signature-contract.md) - Signature verification
4. Examples: Code examples in [API.md](API.md) - cURL, JavaScript, Python

### I'm a DevOps Engineer

1. Setup: [SETUP.md](SETUP.md) - Installation and configuration
2. Docker: [SETUP.md](SETUP.md#docker-setup) - Docker Compose example
3. Deployment: [SETUP.md](SETUP.md#deployment-notes) - Environment variables
4. Architecture: [ARCHITECTURE.md](ARCHITECTURE.md#deployment-notes) - System requirements

### I'm a QA Engineer

1. Testing: [TESTING.md](TESTING.md) - How to run and write tests
2. API: [API.md](API.md) - Test the endpoints
3. Architecture: [ARCHITECTURE.md](ARCHITECTURE.md) - Understand components
4. Contracts: [request-contracts-draft.md](request-contracts-draft.md) - Validation rules

### I'm a Project Manager

1. Overview: [README.md](../README.md) - Project summary
2. Roadmap: [ROADMAP.md](ROADMAP.md) - What's built, what's next
3. Architecture: [ARCHITECTURE.md](ARCHITECTURE.md#future-enhancements) - Feature list

## Common Tasks

### Setting Up Development Environment
→ [SETUP.md](SETUP.md) - Complete guide

### Testing Contact Form Submission
→ [API.md](API.md#3-contact-form-submission-web-forms) with [TESTING.md](TESTING.md)

### Setting Up Webhook Integration
→ [API.md](API.md#4-webhook-contact-form) + [webhook-signature-contract.md](webhook-signature-contract.md)

### Configuring Email Delivery
→ [SETUP.md](SETUP.md#mail-configuration) + [ARCHITECTURE.md](ARCHITECTURE.md#sendmailijob)

### Understanding Data Flow
→ [ARCHITECTURE.md](ARCHITECTURE.md#request-flow-diagrams)

### Understanding Database Schema
→ [ARCHITECTURE.md](ARCHITECTURE.md#database-schema)

### Adding a New Feature
→ [TESTING.md](TESTING.md) + [ROADMAP.md](ROADMAP.md)

### Deploying to Production
→ [SETUP.md](SETUP.md#deployment-notes) + [ROADMAP.md](ROADMAP.md#deployment-todo)

## Current Project Status

✅ **Complete**
- Core infrastructure implemented
- All models, controllers, jobs, services defined
- API routes registered
- Documentation comprehensive

⚠️ **In Progress**
- Test execution (routing issue in test environment)
- Auth enforcement (TODO, not enforced yet)
- Email delivery (code commented, ready to uncomment)

❌ **Not Started**
- File attachment handling (stub)
- Admin site management API
- Analytics dashboard
- Advanced features (A/B testing, templates versioning)

See [ROADMAP.md](ROADMAP.md) for detailed status.

## Key Terms

- **Tenant** - Account owner (User model)
- **Site** - Website/property submitting forms
- **Contact Message** - Incoming email submission from a form
- **Mail Message Event** - Audit log entry for a message state change
- **SiteResolver** - Service that matches requests to sites
- **SendMailJob** - Async job that processes and sends emails
- **Draft Mode** - All auth enforcement disabled (marked with TODO)

## Support

**Question not answered here?**
1. Check the table of contents in the relevant documentation file
2. Search for keywords in the documentation
3. Check [ROADMAP.md](ROADMAP.md) for known issues
4. Review code comments (marked TODO)

**Found an issue?**
- Check `storage/logs/laravel.log` for errors
- See [SETUP.md](SETUP.md#troubleshooting) for common issues
- Check [TESTING.md](TESTING.md#troubleshooting) for test issues

**Want to contribute?**
- Check [ROADMAP.md](ROADMAP.md) for what's needed
- Read [TESTING.md](TESTING.md) for testing requirements
- Follow the existing code style

## Document Maintenance

All documentation was generated on **March 17, 2026**.

**Keep documentation updated:**
- Update docs when adding features
- Keep API.md in sync with code
- Update ROADMAP.md as progress is made
- Add examples as new integrations happen

---

**Start here**: Pick your role above and follow the suggested reading order!

