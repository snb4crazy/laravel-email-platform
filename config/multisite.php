<?php

return [
    // Draft switch only. No hard enforcement implemented yet.
    'enabled' => env('MULTISITE_ENABLED', false),

    // Draft request contract behavior.
    'require_site_key' => env('MULTISITE_REQUIRE_SITE_KEY', false),
    'allow_client_site_id' => env('MULTISITE_ALLOW_CLIENT_SITE_ID', true),

    // Request field names for future standardization.
    'fields' => [
        'site_key' => 'site_key',
        'site_id' => 'site_id',
        'site_domain' => 'site_domain',
        'captcha_token' => 'captcha_token',
        'request_id' => 'request_id',
    ],
];

