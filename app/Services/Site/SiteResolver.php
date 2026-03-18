<?php

namespace App\Services\Site;

use App\Enums\CaptchaProvider;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use App\Models\SiteCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteResolver
{
    /**
     * Resolve site + tenant context from an incoming request.
     *
     * Resolution priority (first match wins):
     *   1. site_key in request body      → browser/form flow
     *   2. X-Key-Id request header       → webhook/server-to-server flow
     *   3. Origin or Referer domain      → browser fallback
     *   4. null → unresolved (pass-through in draft mode)
     *
     * TODO: each step is a stub. Wire DB lookups when auth is enforced.
     */
    public function resolve(Request $request): ResolvedSite
    {
        // 1. site_key in payload
        $siteKey = $request->input(config('multisite.fields.site_key', 'site_key'));
        if ($siteKey) {
            $resolved = $this->resolveByPublicKey((string) $siteKey);
            if ($resolved !== null) {
                $this->log($request, $resolved);

                return $resolved;
            }
        }

        // 2. X-Key-Id header (webhook/server flow)
        $keyId = $request->header('X-Key-Id');
        if ($keyId) {
            $resolved = $this->resolveByCredentialKeyId((string) $keyId);
            if ($resolved !== null) {
                $this->log($request, $resolved);

                return $resolved;
            }
        }

        // 3. Origin / Referer domain match
        $domain = $this->extractDomain($request);
        if ($domain !== null) {
            $resolved = $this->resolveByDomain($domain);
            if ($resolved !== null) {
                $this->log($request, $resolved);

                return $resolved;
            }
        }

        // 4. Unresolved — pass-through in draft mode
        $unresolved = $this->unresolved();
        $this->log($request, $unresolved);

        return $unresolved;
    }

    // -------------------------------------------------------------------------
    // Resolution strategies
    // -------------------------------------------------------------------------

    private function resolveByPublicKey(string $publicKey): ?ResolvedSite
    {
        $site = Site::query()
            ->where('public_key', $publicKey)
            ->where('is_active', true)
            ->first();

        return $site ? self::fromModel($site, ResolvedSite::VIA_SITE_KEY) : null;
    }

    private function resolveByCredentialKeyId(string $keyId): ?ResolvedSite
    {
        $credential = SiteCredential::query()
            ->where('key_id', $keyId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['site' => function ($query) {
                $query->where('is_active', true);
            }])
            ->first();

        if (! $credential || ! $credential->site) {
            return null;
        }

        return self::fromModel($credential->site, ResolvedSite::VIA_CREDENTIAL);
    }

    private function resolveByDomain(string $domain): ?ResolvedSite
    {
        $site = Site::query()
            ->where('domain', strtolower($domain))
            ->where('is_active', true)
            ->first();

        return $site ? self::fromModel($site, ResolvedSite::VIA_DOMAIN) : null;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public static function fromModel(Site $site, string $resolvedVia): ResolvedSite
    {
        return new ResolvedSite(
            siteId: $site->id,
            tenantId: $site->tenant_id,
            authMode: $site->auth_mode,
            captchaProvider: $site->captcha_provider,
            resolvedVia: $resolvedVia,
        );
    }

    private function unresolved(): ResolvedSite
    {
        return new ResolvedSite(
            siteId: null,
            tenantId: null,
            authMode: SiteAuthMode::NONE,
            captchaProvider: CaptchaProvider::NONE,
            resolvedVia: ResolvedSite::VIA_UNRESOLVED,
        );
    }

    private function extractDomain(Request $request): ?string
    {
        foreach (['Origin', 'Referer'] as $header) {
            $value = $request->header($header);
            if ($value) {
                $host = parse_url($value, PHP_URL_HOST);
                if ($host) {
                    return strtolower((string) $host);
                }
            }
        }

        return null;
    }

    private function log(Request $request, ResolvedSite $resolved): void
    {
        Log::debug('SiteResolver result', [
            'resolved_via' => $resolved->resolvedVia,
            'site_id' => $resolved->siteId,
            'tenant_id' => $resolved->tenantId,
            'auth_mode' => $resolved->authMode->value,
            'ip' => $request->ip(),
            'path' => $request->path(),
        ]);
    }
}
