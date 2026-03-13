<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Azure Blob Attachment Storage
    |--------------------------------------------------------------------------
    |
    | This config is prepared for future message attachment uploads.
    | The upload flow is not enabled yet, but all settings are ready.
    |
    */

    'enabled' => env('AZURE_BLOB_ENABLED', false),

    // Logical disk name used by the attachment storage service.
    'disk' => env('AZURE_BLOB_DISK', 'azure_blob'),

    // Optional public base URL for building direct file URLs.
    'public_base_url' => env('AZURE_BLOB_PUBLIC_BASE_URL'),

    'container' => env('AZURE_BLOB_CONTAINER', 'message-attachments'),
    'path_prefix' => env('AZURE_BLOB_PATH_PREFIX', 'messages'),

    // Connection placeholders for future Azure SDK / adapter wiring.
    'account_name' => env('AZURE_BLOB_ACCOUNT_NAME'),
    'account_key' => env('AZURE_BLOB_ACCOUNT_KEY'),
    'endpoint_suffix' => env('AZURE_BLOB_ENDPOINT_SUFFIX', 'core.windows.net'),
    'connection_string' => env('AZURE_BLOB_CONNECTION_STRING'),
];
