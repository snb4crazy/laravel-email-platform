<?php

namespace App\Enums;

enum CredentialType: string
{
    case API_KEY = 'api_key';
    case HMAC = 'hmac';
    case OAUTH_TOKEN = 'oauth_token';
}
