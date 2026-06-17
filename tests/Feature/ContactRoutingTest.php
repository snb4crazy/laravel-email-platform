<?php

namespace Tests\Feature;

use App\Enums\CredentialType;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use App\Models\SiteCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ContactRoutingTest extends TestCase
{
    public function test_contact_submission_routes_to_site_notification_email(): void
    {
        $site = Site::query()->create([
            'name' => 'Portfolio',
            'domain' => 'portfolio.local',
            'public_key' => 'pk_'.Str::random(16),
            'notification_email' => 'owner@portfolio.local',
            'is_active' => true,
        ]);

        $payload = [
            'site_key' => $site->public_key,
            'name' => 'Visitor Name',
            'email' => 'visitor@example.com',
            'subject' => 'Hi',
            'message' => 'Can we work together?',
        ];

        $this->postJson('/api/contact', $payload)
            ->assertStatus(202);

        $this->assertDatabaseHas('mail_messages', [
            'site_id' => $site->id,
            'to_email' => 'owner@portfolio.local',
            'reply_to' => 'visitor@example.com',
            'source' => 'web',
        ]);
    }

    public function test_webhook_submission_can_resolve_site_from_key_id_header(): void
    {
        $site = Site::query()->create([
            'name' => 'Webhook Site',
            'domain' => 'webhook.local',
            'public_key' => 'pk_'.Str::random(16),
            'notification_email' => 'webhook-owner@example.com',
            'is_active' => true,
        ]);

        $credential = SiteCredential::query()->create([
            'site_id' => $site->id,
            'name' => 'Webhook Key',
            'credential_type' => CredentialType::HMAC,
            'key_id' => 'kid_'.Str::random(18),
            'secret_hash' => 'demo-secret',
            'is_active' => true,
        ]);

        $this->withHeaders(['X-Key-Id' => $credential->key_id])
            ->postJson('/api/webhook/contact-form', [
                'name' => 'Webhook Sender',
                'email' => 'sender@example.com',
                'message' => 'Webhook payload',
            ])
            ->assertStatus(202);

        $this->assertDatabaseHas('mail_messages', [
            'site_id' => $site->id,
            'to_email' => 'webhook-owner@example.com',
            'reply_to' => 'sender@example.com',
            'source' => 'webhook',
        ]);
    }

    public function test_contact_rejects_unresolved_site_when_auth_is_enforced(): void
    {
        config(['draft_auth.enforce' => true]);

        $this->postJson('/api/contact', [
            'name' => 'No Site',
            'email' => 'no-site@example.com',
            'message' => 'Message without site.',
        ])->assertStatus(422);
    }

    public function test_contact_api_key_mode_requires_valid_site_api_key(): void
    {
        config(['draft_auth.enforce' => true]);

        $site = Site::query()->create([
            'name' => 'API Key Site',
            'domain' => 'apikey.local',
            'public_key' => 'pk_'.Str::random(16),
            'notification_email' => 'owner@apikey.local',
            'auth_mode' => SiteAuthMode::API_KEY,
            'is_active' => true,
        ]);

        $apiKey = 'live-api-key-value';

        SiteCredential::query()->create([
            'site_id' => $site->id,
            'name' => 'Primary API Key',
            'credential_type' => CredentialType::API_KEY,
            'key_id' => 'kid_'.Str::random(12),
            'secret_hash' => bcrypt($apiKey),
            'secret_encrypted' => $apiKey,
            'is_active' => true,
        ]);

        $payload = [
            'site_key' => $site->public_key,
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'message' => 'Auth protected request.',
        ];

        $this->postJson('/api/contact', $payload)->assertStatus(401);

        $this->withHeaders(['X-Api-Key' => $apiKey])
            ->postJson('/api/contact', $payload)
            ->assertStatus(202);
    }

    public function test_contact_captcha_mode_requires_valid_token(): void
    {
        config(['draft_auth.enforce' => true]);

        $site = Site::query()->create([
            'name' => 'Captcha Site',
            'domain' => 'captcha.local',
            'public_key' => 'pk_'.Str::random(16),
            'notification_email' => 'owner@captcha.local',
            'auth_mode' => SiteAuthMode::CAPTCHA,
            'captcha_provider' => 'turnstile',
            'is_active' => true,
        ]);

        SiteCredential::query()->create([
            'site_id' => $site->id,
            'name' => 'Turnstile Secret',
            'credential_type' => CredentialType::CAPTCHA_SECRET,
            'key_id' => 'cap_'.Str::random(12),
            'secret_encrypted' => 'turnstile-secret',
            'is_active' => true,
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        $payload = [
            'site_key' => $site->public_key,
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'message' => 'Captcha protected request.',
        ];

        $this->postJson('/api/contact', $payload)->assertStatus(422);

        $this->postJson('/api/contact', array_merge($payload, [
            'captcha_token' => 'token-ok',
        ]))->assertStatus(202);
    }
}
