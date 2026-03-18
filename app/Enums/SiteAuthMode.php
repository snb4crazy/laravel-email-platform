<?php

namespace App\Enums;

enum SiteAuthMode: string
{
    case NONE = 'none';
    case CAPTCHA = 'captcha';
    case API_KEY = 'api_key';
    case HMAC = 'hmac';
    case OAUTH_TOKEN = 'oauth_token';
}
