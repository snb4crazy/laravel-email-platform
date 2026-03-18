# Testing Guide

## Overview

This document covers the testing strategy, how to write tests, and how to run the test suite for the Email Platform.

## Test Structure

```
tests/
├── Feature/
│   ├── ApiRoutesTest.php          # API endpoint integration tests
│   ├── ContactSubmissionTest.php   # TODO: Contact submission workflow
│   ├── JobsTest.php                # TODO: Job dispatch & execution
│   └── TemplateResolutionTest.php  # TODO: Template resolution logic
├── Unit/
│   ├── Models/
│   │   ├── MailMessageTest.php     # TODO: Model relationships
│   │   ├── SiteTest.php            # TODO: Site model
│   │   └── UserTest.php            # TODO: User/tenant model
│   ├── Services/
│   │   ├── SiteResolverTest.php    # TODO: Site resolver logic
│   │   └── MailTemplateResolverTest.php # TODO: Template resolution
│   └── Middleware/
│       ├── DraftContactAuthTest.php     # TODO: Auth middleware
│       └── DraftWebhookSignatureTest.php # TODO: Webhook middleware
└── TestCase.php
```

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/ApiRoutesTest.php
```

### Run Tests with Coverage
```bash
php artisan test --coverage
```

Prerequisite:
- CLI PHP must have a coverage driver loaded: `pcov` or `xdebug`

Check it with:
```bash
php -m | grep -Ei 'pcov|xdebug'
```

### Run Tests in Parallel
```bash
php artisan test --parallel
```

Prerequisite:
- `brianium/paratest` must be installed in `require-dev`

### Run Unit Tests Only
```bash
php artisan test tests/Unit
```

### Run Feature Tests Only
```bash
php artisan test tests/Feature
```

## Test Configuration

### PHPUnit Config (`phpunit.xml`)
- In-memory SQLite database for testing
- Array cache driver
- Sync queue driver (no async jobs)
- Array mail mailer (no actual emails sent)
- Testing environment: `APP_ENV=testing`

### Tooling Prerequisites

- Parallel test execution requires `brianium/paratest`
- Coverage requires a PHP coverage extension loaded in the CLI runtime:
  - `pcov` (recommended for test coverage)
  - or `xdebug`

Examples on macOS/Homebrew PHP:

```bash
composer require --dev brianium/paratest:^7
printf '\n' | pecl install pcov
php -m | grep -Ei 'pcov|xdebug'
```

### Database Reset
Tests run with a fresh in-memory database. Use `RefreshDatabase` trait to migrate before each test:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_something()
    {
        // Database is migrated and fresh
    }
}
```

## Writing Tests

### Feature Test Template

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class YourFeatureTest extends TestCase
{
    public function test_successful_scenario(): void
    {
        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
        ]);

        $response
            ->assertStatus(202)
            ->assertJson([
                'message' => 'Contact request received.',
            ]);
    }

    public function test_validation_errors(): void
    {
        $response = $this->postJson('/api/contact', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }
}
```

### Unit Test Template

```php
<?php

namespace Tests\Unit;

use App\Models\MailMessage;
use PHPUnit\Framework\TestCase;

class MailMessageTest extends TestCase
{
    public function test_can_create_message(): void
    {
        $message = new MailMessage([
            'source' => 'web',
            'to_email' => 'test@example.com',
            'status' => 'queued',
        ]);

        $this->assertEquals('queued', $message->status);
    }
}
```

## Existing Tests

### `tests/Feature/ApiRoutesTest.php`

Tests the API endpoints with actual HTTP requests.

**Tests**:
1. `test_health_endpoint_returns_ok_status()`
   - GET /api/health → 200 with { "status": "ok" }

2. `test_version_endpoint_returns_application_metadata()`
   - GET /api/version → 200 with app and laravel version

3. `test_contact_endpoint_accepts_valid_payload()`
   - POST /api/contact with valid data → 202

4. `test_webhook_contact_form_endpoint_accepts_valid_payload()`
   - POST /api/webhook/contact-form with valid data → 202

5. `test_contact_endpoint_validates_required_fields()`
   - POST /api/contact with empty body → 422 with validation errors

### `tests/Unit/ExampleTest.php`

Simple unit test demonstrating PHPUnit assertions.

## TODO Test Cases

### Feature Tests to Implement

```php
// ContactSubmissionTest.php
- test_contact_job_is_queued_on_valid_submission()
- test_mail_message_record_is_created()
- test_mail_message_events_are_recorded()
- test_can_submit_with_optional_subject()
- test_can_submit_with_optional_file_url()
- test_unresolved_site_submission_works()
- test_webhook_intake_creates_different_event_type()
- test_validation_error_on_invalid_email()
- test_validation_error_on_message_too_long()

// JobsTest.php
- test_send_mail_job_creates_message()
- test_send_mail_job_records_queued_event()
- test_send_mail_job_resolves_template()
- test_send_mail_job_handles_failure_gracefully()
- test_failed_job_records_error_event()

// TemplateResolutionTest.php
- test_resolves_blade_template_first()
- test_resolves_tenant_template_second()
- test_resolves_global_template_third()
- test_logs_template_resolution()

// SiteResolutionTest.php
- test_resolves_by_site_key_first()
- test_resolves_by_credential_header_second()
- test_resolves_by_domain_third()
- test_returns_unresolved_when_no_match()
```

### Unit Tests to Implement

```php
// Models
- MailMessageTest: relationships, recordEvent()
- MailMessageEventTest: payload casting
- SiteTest: relationships, credentials
- UserTest: relationships, multi-tenant scoping

// Services
- SiteResolverTest: all resolution strategies
- MailTemplateResolverTest: fallback chain
- MessageAttachmentStorageServiceTest: upload/download

// Middleware
- DraftContactAuthMiddlewareTest: site resolution
- DraftWebhookSignatureMiddlewareTest: site resolution
```

## Test Assertions Reference

### Response Assertions (Feature Tests)

```php
// HTTP Status
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertAccepted();      // 202
$response->assertNoContent();
$response->assertNotFound();
$response->assertUnauthorized();
$response->assertForbidden();
$response->assertConflict();
$response->assertUnprocessable(); // 422

// JSON
$response->assertJson(['key' => 'value']);
$response->assertJsonStructure([
    'data' => [
        'id',
        'name',
        'email',
    ]
]);
$response->assertJsonMissing(['error' => true]);
$response->assertJsonPath('status', 'ok');

// Validation
$response->assertJsonValidationErrors(['name', 'email']);
$response->assertJsonValidationErrorCount(2);

// Headers
$response->assertHeader('Content-Type', 'application/json');
$response->assertHeaderMissing('X-Special-Header');
```

### Model Assertions (Unit Tests)

```php
// Database
$this->assertDatabaseHas('mail_messages', [
    'to_email' => 'test@example.com',
    'status' => 'sent',
]);
$this->assertDatabaseMissing('mail_messages', [
    'to_email' => 'nonexistent@example.com',
]);

// Model
$model = MailMessage::first();
$this->assertNotNull($model);
$this->assertEquals('queued', $model->status);
$this->assertTrue($model->isQueued());

// Relations
$this->assertCount(3, $message->events);
$this->assertInstanceOf(User::class, $site->tenant);
```

## Debugging Tests

### Print Debug Info
```php
// In test
dd($response->json());              // Dump and die
dump($response->getContent());      // Just dump
error_log('Debug: ' . $message);    // Log to laravel.log
```

### Run Single Test with Output
```bash
php artisan test tests/Feature/ApiRoutesTest.php --verbose
```

### Check Database State After Test Fails
```php
public function test_something(): void
{
    $response = $this->postJson('/api/contact', [...]);
    
    // If test fails, inspect database
    dump(MailMessage::all());
    dump(MailMessageEvent::all());
}
```

## Best Practices

1. **Test Behavior, Not Implementation**
   - Test what the API returns, not how internally it works

2. **One Assert Per Concept**
   - Don't test multiple unrelated things in one test
   - Use multiple assertions for same concept

3. **Descriptive Test Names**
   - Use `test_` prefix
   - Describe the scenario and expected outcome
   - `test_contact_endpoint_returns_202_on_valid_submission()`

4. **Use Factories for Data**
   - TODO: Create model factories in `database/factories/`
   - Use `MailMessage::factory()->create()` in tests

5. **Isolate External Dependencies**
   - Mock external services (email, SMS, etc.)
   - Use `Mail::fake()` to prevent actual mail sending
   - Use `Queue::fake()` to prevent actual job dispatch

6. **Test Happy & Sad Paths**
   - Test success scenario
   - Test validation errors
   - Test server errors
   - Test edge cases

## Continuous Integration

### Running Tests in CI/CD

```bash
# In GitHub Actions, GitLab CI, etc.
php artisan migrate --env=testing
php artisan test --coverage
```

### Coverage Reports
```bash
php artisan test --coverage --coverage-html=coverage/
# Open coverage/index.html in browser
```

## Troubleshooting

### Tests Get 404 for Routes

If routes aren't found in tests:
1. Ensure routes are defined in `routes/api.php` or `routes/web.php`
2. Check `bootstrap/app.php` routing configuration
3. Clear config cache: `php artisan config:clear`
4. Ensure `TestCase` extends `Illuminate\Foundation\Testing\TestCase`

### Database Errors

If migrations fail in tests:
1. Check `phpunit.xml` DB config (should be `:memory:` for SQLite)
2. Ensure all foreign key references exist
3. Check migration order and `pending` status

### Jobs Not Dispatched

Tests use sync queue driver by default:
- Jobs execute immediately
- Perfect for testing job logic
- Configure in `phpunit.xml`: `QUEUE_CONNECTION=sync`

### Email Not Sent

Tests use array mailer driver:
- Emails don't actually send
- Access with `Mail::getMessagesForTesting()`
- Use `Mail::fake()` to prevent side effects

## Resources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/)
- [Laravel Testing Helpers](https://laravel.com/docs/http-tests)

---

Last updated: March 17, 2026

