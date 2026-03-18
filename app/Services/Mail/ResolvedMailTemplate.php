<?php

namespace App\Services\Mail;

/**
 * Resolved template data — output of MailTemplateResolver.
 * Carries final rendered strings ready to pass into a Mailable.
 */
final class ResolvedMailTemplate
{
    public function __construct(
        public readonly string $subject,
        public readonly string $bodyHtml,
        public readonly string $bodyText,
        public readonly string $resolvedVia, // 'db_tenant' | 'db_global' | 'blade'
    ) {}
}
