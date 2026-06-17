<?php

return [
    // Switch for future enforcement rollout.
    'enforce' => env('DRAFT_AUTH_ENFORCE', false),

    // Extra verbose app-level debug logging for request/job flow diagnostics.
    'extended_debug' => env('EXTENDED_DEBUG', false),

    'contact' => [
        // TODO: require API key for /api/contact.
        'require_api_key' => env('DRAFT_CONTACT_REQUIRE_API_KEY', false),
        'header' => env('DRAFT_CONTACT_API_KEY_HEADER', 'X-Api-Key'),

        // TODO: wire captcha verification.
        'captcha' => [
            'enabled' => env('DRAFT_CONTACT_CAPTCHA_ENABLED', false),
            'provider' => env('DRAFT_CONTACT_CAPTCHA_PROVIDER', 'turnstile'),
            'secret' => env('DRAFT_CONTACT_CAPTCHA_SECRET'),
        ],
    ],

    'webhook' => [
        // TODO: enforce HMAC signature checks.
        'require_signature' => env('DRAFT_WEBHOOK_REQUIRE_SIGNATURE', false),
        'algorithm' => env('DRAFT_WEBHOOK_SIGNATURE_ALGO', 'sha256'),
        'allowed_clock_skew_seconds' => (int) env('DRAFT_WEBHOOK_ALLOWED_CLOCK_SKEW_SECONDS', 300),

        'headers' => [
            'key_id' => env('DRAFT_WEBHOOK_HEADER_KEY_ID', 'X-Key-Id'),
            'timestamp' => env('DRAFT_WEBHOOK_HEADER_TIMESTAMP', 'X-Timestamp'),
            'nonce' => env('DRAFT_WEBHOOK_HEADER_NONCE', 'X-Nonce'),
            'content_sha256' => env('DRAFT_WEBHOOK_HEADER_CONTENT_SHA256', 'X-Content-SHA256'),
            'signature' => env('DRAFT_WEBHOOK_HEADER_SIGNATURE', 'X-Signature'),
        ],
    ],
];
