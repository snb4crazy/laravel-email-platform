<?php

namespace Database\Seeders;

use App\Enums\CredentialType;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use App\Models\SiteCredential;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUseCasesSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->firstOrCreate(
            ['email' => 'demo-owner@example.com'],
            ['name' => 'Demo Owner', 'password' => 'password']
        );

        $portfolioSite = Site::query()->updateOrCreate(
            ['public_key' => 'demo_portfolio_pk'],
            [
                'tenant_id' => $owner->id,
                'name' => 'Freelancer Portfolio',
                'domain' => 'portfolio.demo.local',
                'notification_email' => 'freelancer-inbox@example.com',
                'auth_mode' => SiteAuthMode::NONE,
                'is_active' => true,
                'metadata' => [
                    'use_case' => 'Personal portfolio lead capture',
                ],
            ]
        );

        $agencySite = Site::query()->updateOrCreate(
            ['public_key' => 'demo_agency_pk'],
            [
                'tenant_id' => $owner->id,
                'name' => 'Agency Contact Hub',
                'domain' => 'agency.demo.local',
                'notification_email' => 'agency-sales@example.com',
                'auth_mode' => SiteAuthMode::API_KEY,
                'is_active' => true,
                'metadata' => [
                    'use_case' => 'Multiple client websites routed into one support inbox',
                ],
            ]
        );

        SiteCredential::query()->updateOrCreate(
            ['key_id' => 'demo_agency_key_01'],
            [
                'site_id' => $agencySite->id,
                'name' => 'Agency API Key',
                'credential_type' => CredentialType::API_KEY,
                'secret_hash' => 'demo-secret-placeholder',
                'is_active' => true,
            ]
        );

        $saasWebhookSite = Site::query()->updateOrCreate(
            ['public_key' => 'demo_saas_pk'],
            [
                'tenant_id' => $owner->id,
                'name' => 'SaaS Signup Webhook',
                'domain' => 'saas.demo.local',
                'notification_email' => 'saas-growth@example.com',
                'auth_mode' => SiteAuthMode::HMAC,
                'is_active' => true,
                'metadata' => [
                    'use_case' => 'Webhook intake from external form provider',
                ],
            ]
        );

        SiteCredential::query()->updateOrCreate(
            ['key_id' => 'demo_webhook_key_01'],
            [
                'site_id' => $saasWebhookSite->id,
                'name' => 'Webhook HMAC Key',
                'credential_type' => CredentialType::HMAC,
                'secret_hash' => 'demo-webhook-secret-placeholder',
                'is_active' => true,
            ]
        );

        $this->command?->info('Demo use cases seeded.');
    }
}
