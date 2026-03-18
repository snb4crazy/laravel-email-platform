<?php

namespace App\Enums;

enum CaptchaProvider: string
{
    case NONE = 'none';
    case TURNSTILE = 'turnstile';
    case RECAPTCHA_V2 = 'recaptcha_v2';
    case RECAPTCHA_V3 = 'recaptcha_v3';
    case HCAPTCHA = 'hcaptcha';
}

