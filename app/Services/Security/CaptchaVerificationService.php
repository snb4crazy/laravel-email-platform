<?php

namespace App\Services\Security;

use App\Enums\CaptchaProvider;
use App\Enums\CredentialType;
use App\Models\Site;
use App\Models\SiteCredential;
use Illuminate\Support\Facades\Http;

class CaptchaVerificationService
{
    public function verifyForSite(Site $site, string $token, ?string $ip): bool
    {
        $provider = $site->captcha_provider ?? CaptchaProvider::NONE;
        $secret = $this->resolveSecret($site);

        if (! $secret || $provider === CaptchaProvider::NONE) {
            return false;
        }

        return match ($provider) {
            CaptchaProvider::TURNSTILE => $this->verifyTurnstile($secret, $token, $ip),
            CaptchaProvider::RECAPTCHA_V2,
            CaptchaProvider::RECAPTCHA_V3 => $this->verifyRecaptcha($secret, $token, $ip),
            CaptchaProvider::HCAPTCHA => $this->verifyHcaptcha($secret, $token, $ip),
            default => false,
        };
    }

    private function resolveSecret(Site $site): ?string
    {
        $credential = $site->credentials()
            ->where('credential_type', CredentialType::CAPTCHA_SECRET->value)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        if ($credential instanceof SiteCredential && $credential->secret_encrypted) {
            return $credential->secret_encrypted;
        }

        if ($site->captcha_secret) {
            return $site->captcha_secret;
        }

        return config('draft_auth.contact.captcha.secret');
    }

    private function verifyTurnstile(string $secret, string $token, ?string $ip): bool
    {
        $response = Http::asForm()
            ->timeout(8)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

        if (! $response->ok()) {
            return false;
        }

        return (bool) $response->json('success', false);
    }

    private function verifyRecaptcha(string $secret, string $token, ?string $ip): bool
    {
        $response = Http::asForm()
            ->timeout(8)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

        if (! $response->ok()) {
            return false;
        }

        return (bool) $response->json('success', false);
    }

    private function verifyHcaptcha(string $secret, string $token, ?string $ip): bool
    {
        $response = Http::asForm()
            ->timeout(8)
            ->post('https://hcaptcha.com/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

        if (! $response->ok()) {
            return false;
        }

        return (bool) $response->json('success', false);
    }
}
