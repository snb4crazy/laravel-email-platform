<?php

namespace App\Http\Controllers;

use App\Enums\CredentialType;
use App\Models\Site;
use Illuminate\View\View;

class SiteIntegrationController extends Controller
{
    public function show(Site $site): View
    {
        $this->authorize('view', $site);

        $apiKeyCredential = $site->credentials()
            ->where('credential_type', CredentialType::API_KEY->value)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        $hmacCredential = $site->credentials()
            ->where('credential_type', CredentialType::HMAC->value)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        return view('portal.sites.integration', [
            'site' => $site,
            'apiUrl' => rtrim((string) config('app.url'), '/'),
            'apiKeyHeader' => (string) config('draft_auth.contact.header', 'X-Api-Key'),
            'siteKey' => $site->public_key,
            'apiKeyHint' => $apiKeyCredential ? 'Use your generated API key value' : 'Create API credential first',
            'keyId' => $hmacCredential?->key_id ?? 'replace-with-your-key-id',
        ]);
    }
}
