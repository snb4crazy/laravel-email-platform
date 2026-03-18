<?php

namespace App\Services\Mail;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\View;

class MailTemplateResolver
{
    /**
     * Resolve the best template for a given event type and optional tenant.
     *
     * Priority waterfall:
     *  1. Active DB template scoped to tenant + event type
     *  2. Active DB global template (tenant_id = null) for event type
     *  3. Blade fallback: resources/views/mail/{event_type}.blade.php
     *
     * @param  string  $eventType  One of MailTemplate::EVENT_* constants
     * @param  array  $vars  Placeholder values for interpolation
     * @param  int|null  $tenantId  Nullable until tenancy is implemented
     */
    public function resolve(
        string $eventType,
        array $vars = [],
        ?int $tenantId = null,
    ): ResolvedMailTemplate {
        // TODO: wrap DB lookups in Cache::remember() keyed by (tenant_id, event_type)
        // once queue throughput justifies it.

        // 1. Tenant-specific DB template
        if ($tenantId !== null) {
            $template = MailTemplate::active()
                ->forTenant($tenantId)
                ->forEvent($eventType)
                ->default()
                ->first();

            if ($template !== null) {
                return new ResolvedMailTemplate(
                    subject: $template->renderSubject($vars),
                    bodyHtml: $template->renderBodyHtml($vars),
                    bodyText: $template->renderBodyText($vars),
                    resolvedVia: 'db_tenant',
                );
            }
        }

        // 2. Global DB template (tenant_id = null)
        $globalTemplate = MailTemplate::active()
            ->forTenant(null)
            ->forEvent($eventType)
            ->default()
            ->first();

        if ($globalTemplate !== null) {
            return new ResolvedMailTemplate(
                subject: $globalTemplate->renderSubject($vars),
                bodyHtml: $globalTemplate->renderBodyHtml($vars),
                bodyText: $globalTemplate->renderBodyText($vars),
                resolvedVia: 'db_global',
            );
        }

        // 3. Blade fallback — renders resources/views/mail/{event_type}.blade.php
        // Map event type constant to a blade view name.
        $bladeView = 'mail.'.str_replace('_', '.', $eventType);

        // Normalise to mail.contact if the specific view doesn't exist.
        if (! View::exists($bladeView)) {
            $bladeView = 'mail.contact';
        }

        $bodyHtml = view($bladeView, array_merge($vars, [
            'subject' => $vars['subject'] ?? null,
        ]))->render();

        return new ResolvedMailTemplate(
            subject: $vars['subject'] ?? 'New Message',
            bodyHtml: $bodyHtml,
            bodyText: strip_tags($bodyHtml),
            resolvedVia: 'blade',
        );
    }
}
