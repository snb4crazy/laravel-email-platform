<?php

namespace Tests\Feature;

use App\Enums\CredentialType;
use App\Models\Site;
use App\Models\SiteCredential;
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
}
