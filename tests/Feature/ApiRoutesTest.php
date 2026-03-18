<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    public function test_health_endpoint_returns_ok_status(): void
    {
        $response = $this->json('GET', '/api/health');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    public function test_version_endpoint_returns_application_metadata(): void
    {
        $response = $this->json('GET', '/api/version');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'app',
                'laravel',
            ]);
    }

    public function test_contact_endpoint_accepts_valid_payload(): void
    {
        $response = $this->json('POST', '/api/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Partnership',
            'message' => 'Hi, this is a test message.',
        ]);

        $response
            ->assertStatus(202)
            ->assertJson([
                'message' => 'Contact request received.',
            ]);
    }

    public function test_webhook_contact_form_endpoint_accepts_valid_payload(): void
    {
        $response = $this->json('POST', '/api/webhook/contact-form', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Webhook test message.',
        ]);

        $response
            ->assertStatus(202)
            ->assertJson([
                'message' => 'Contact request received.',
            ]);
    }

    public function test_contact_endpoint_validates_required_fields(): void
    {
        $response = $this->json('POST', '/api/contact', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'message',
            ]);
    }
}
