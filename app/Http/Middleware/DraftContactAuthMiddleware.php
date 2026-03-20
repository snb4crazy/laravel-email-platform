<?php

namespace App\Http\Middleware;

use App\Enums\CredentialType;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use App\Models\SiteCredential;
use App\Services\Security\CaptchaVerificationService;
use App\Services\Site\SiteResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DraftContactAuthMiddleware
{
    public function __construct(
        private readonly SiteResolver $resolver,
        private readonly CaptchaVerificationService $captcha,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Resolve site context and attach it to the request for downstream use.
            $resolved = $this->resolver->resolve($request);
            $request->attributes->set('resolved_site', $resolved);

            $this->debug('DraftContactAuthMiddleware received request', [
                'path' => $request->path(),
                'resolved_via' => $resolved->resolvedVia,
                'site_id' => $resolved->siteId,
                'auth_mode' => $resolved->authMode->value,
                'enforce' => (bool) config('draft_auth.enforce', false),
            ]);

            if (! config('draft_auth.enforce', false)) {
                $this->debug('DraftContactAuthMiddleware pass-through: enforcement disabled');

                return $next($request);
            }

            if (! $resolved->isResolved() || ! $resolved->siteId) {
                $this->debug('DraftContactAuthMiddleware failed: unresolved site context');

                return response()->json([
                    'message' => 'Unable to resolve site context. Provide site_key or a known domain.',
                ], 422);
            }

            $site = Site::query()->whereKey($resolved->siteId)->where('is_active', true)->first();
            if (! $site) {
                $this->debug('DraftContactAuthMiddleware failed: resolved site inactive or missing', [
                    'resolved_site_id' => $resolved->siteId,
                ]);

                return response()->json(['message' => 'Resolved site is inactive or not found.'], 422);
            }

            if ($resolved->authMode === SiteAuthMode::NONE) {
                $this->debug('DraftContactAuthMiddleware success: auth_mode none');

                return $next($request);
            }

            if ($resolved->authMode === SiteAuthMode::CAPTCHA) {
                $tokenField = config('multisite.fields.captcha_token', 'captcha_token');
                $captchaToken = (string) $request->input($tokenField, '');

                if ($captchaToken === '') {
                    $this->debug('DraftContactAuthMiddleware failed: captcha token missing', [
                        'token_field' => $tokenField,
                    ]);

                    return response()->json(['message' => 'captcha_token is required for this site.'], 422);
                }

                if (! $this->captcha->verifyForSite($site, $captchaToken, $request->ip())) {
                    $this->debug('DraftContactAuthMiddleware failed: captcha verification failed', [
                        'site_id' => $site->id,
                        'captcha_provider' => $site->captcha_provider?->value,
                    ]);

                    return response()->json(['message' => 'Captcha verification failed.'], 422);
                }

                $this->debug('DraftContactAuthMiddleware success: captcha verified', [
                    'site_id' => $site->id,
                ]);

                return $next($request);
            }

            if ($resolved->authMode === SiteAuthMode::API_KEY) {
                $header = (string) config('draft_auth.contact.header', 'X-Api-Key');
                $apiKey = (string) ($request->header($header) ?? $request->input('api_key', ''));

                if ($apiKey === '') {
                    $this->debug('DraftContactAuthMiddleware failed: API key missing', [
                        'header' => $header,
                    ]);

                    return response()->json(['message' => 'API key is required for this site.'], 401);
                }

                $credentials = SiteCredential::query()
                    ->where('site_id', $site->id)
                    ->where('credential_type', CredentialType::API_KEY->value)
                    ->where('is_active', true)
                    ->where(function ($query): void {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->latest('id')
                    ->get();

                if ($credentials->isEmpty()) {
                    $this->debug('DraftContactAuthMiddleware failed: no active API credentials', [
                        'site_id' => $site->id,
                    ]);

                    return response()->json(['message' => 'Site API key is not configured.'], 401);
                }

                $matchedCredential = $credentials->first(function (SiteCredential $credential) use ($apiKey): bool {
                    $validByHash = $credential->secret_hash && Hash::check($apiKey, $credential->secret_hash);
                    $validByEncrypted = $credential->secret_encrypted && hash_equals($credential->secret_encrypted, $apiKey);

                    return $validByHash || $validByEncrypted;
                });

                if (! $matchedCredential) {
                    $this->debug('DraftContactAuthMiddleware failed: API key mismatch', [
                        'site_id' => $site->id,
                        'credential_count' => $credentials->count(),
                    ]);

                    return response()->json(['message' => 'Invalid API key.'], 401);
                }

                $matchedCredential->forceFill(['last_used_at' => now()])->save();

                $this->debug('DraftContactAuthMiddleware success: API key verified', [
                    'site_id' => $site->id,
                    'credential_id' => $matchedCredential->id,
                ]);

                return $next($request);
            }

            $this->debug('DraftContactAuthMiddleware failed: unsupported auth mode', [
                'auth_mode' => $resolved->authMode->value,
            ]);

            return response()->json([
                'message' => 'Selected auth mode is not available for /api/contact yet.',
            ], 403);
        } catch (\Throwable $e) {
            Log::error('DraftContactAuthMiddleware exception', [
                'path' => $request->path(),
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }

    private function debug(string $message, array $context = []): void
    {
        if (! config('draft_auth.extended_debug', false)) {
            return;
        }

        Log::debug($message, $context);
    }
}
