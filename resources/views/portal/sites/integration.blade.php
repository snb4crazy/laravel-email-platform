@extends('layouts.portal')

@section('header', 'API Integration: ' . $site->name)

@section('main')
<div class="max-w-5xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('sites.show', $site) }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Site</a>
        <a href="{{ route('sites.credentials.index', $site) }}" class="text-sm text-indigo-600 hover:underline">Manage Credentials</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-5 text-sm space-y-2">
        <h2 class="font-semibold text-gray-800">Integration Values</h2>
        <div><span class="inline-block w-36 text-gray-500">API Base URL</span><code>{{ $apiUrl }}</code></div>
        <div><span class="inline-block w-36 text-gray-500">Site Key</span><code>{{ $siteKey }}</code></div>
        <div><span class="inline-block w-36 text-gray-500">API Key Header</span><code>{{ $apiKeyHeader }}</code></div>
        <div><span class="inline-block w-36 text-gray-500">Webhook Key ID</span><code>{{ $keyId }}</code></div>
        <p class="pt-2 text-xs text-gray-500">
            Keep secrets (API keys, captcha secrets, HMAC secret) in Site Credentials only.
            This page intentionally shows only safe-to-share values.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Web Form Request (POST /api/contact)</h3>
            <p class="text-xs text-gray-500">Use this from React or any frontend app.</p>
            <pre class="bg-gray-900 text-gray-100 text-xs p-4 rounded-md overflow-x-auto"><code>curl -X POST {{ $apiUrl }}/api/contact \
  -H "Content-Type: application/json" \
  -H "{{ $apiKeyHeader }}: &lt;{{ $apiKeyHint }}&gt;" \
  -d '{
    "site_key": "{{ $siteKey }}",
    "name": "Jane Doe",
    "email": "jane@example.com",
    "subject": "Question",
    "message": "Hello from contact form",
    "captcha_token": "&lt;captcha-token-if-auth-mode-captcha&gt;"
  }'</code></pre>
        </section>

        <section class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
            <h3 class="font-semibold text-gray-800">Webhook Request (POST /api/webhook/contact-form)</h3>
            <p class="text-xs text-gray-500">Use this for server-to-server integrations.</p>
            <pre class="bg-gray-900 text-gray-100 text-xs p-4 rounded-md overflow-x-auto"><code>curl -X POST {{ $apiUrl }}/api/webhook/contact-form \
  -H "Content-Type: application/json" \
  -H "X-Key-Id: {{ $keyId }}" \
  -H "X-Timestamp: 2026-03-19T12:00:00Z" \
  -H "X-Nonce: random-unique-value" \
  -H "X-Content-SHA256: &lt;body-sha256&gt;" \
  -H "X-Signature: sha256=&lt;hmac-signature&gt;" \
  -d '{
    "site_key": "{{ $siteKey }}",
    "name": "Webhook Sender",
    "email": "sender@example.com",
    "message": "Webhook payload"
  }'</code></pre>
        </section>
    </div>

    <section class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
        <h3 class="font-semibold text-gray-800">Frontend fetch() Example</h3>
        <pre class="bg-gray-900 text-gray-100 text-xs p-4 rounded-md overflow-x-auto"><code>const response = await fetch('{{ $apiUrl }}/api/contact', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    '{{ $apiKeyHeader }}': '&lt;api-key-if-required&gt;'
  },
  body: JSON.stringify({
    site_key: '{{ $siteKey }}',
    name: 'Jane Doe',
    email: 'jane@example.com',
    message: 'Hello from SPA',
    captcha_token: '&lt;captcha-token-if-required&gt;'
  })
});

const data = await response.json();
console.log(response.status, data);</code></pre>
    </section>
</div>
@endsection

