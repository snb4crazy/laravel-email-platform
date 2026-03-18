<?php

namespace App\Services\Site;

use App\Enums\CaptchaProvider;
use App\Enums\SiteAuthMode;

/**
 * Resolved site context — output of SiteResolver.
 *
 * Carries everything downstream code needs about the matched site
 * without them depending on the Site model or DB directly.
 */
final class ResolvedSite
{
    public const VIA_SITE_KEY     = 'site_key';
    public const VIA_CREDENTIAL   = 'credential_key_id';
    public const VIA_DOMAIN       = 'domain';
    public const VIA_UNRESOLVED   = 'unresolved';

    public function __construct(
        public readonly ?int           $siteId,
        public readonly ?int           $tenantId,
        public readonly SiteAuthMode   $authMode,
        public readonly CaptchaProvider $captchaProvider,
        public readonly string         $resolvedVia,
    ) {}

    public function isResolved(): bool
    {
        return $this->resolvedVia !== self::VIA_UNRESOLVED;
    }
}

