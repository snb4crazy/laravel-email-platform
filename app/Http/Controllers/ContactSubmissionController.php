<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactSubmissionRequest;
use App\Http\Requests\WebhookContactRequest;
use App\Jobs\SendMailJob;
use App\Services\Site\ResolvedSite;
use Illuminate\Http\JsonResponse;

class ContactSubmissionController extends Controller
{
    public function store(ContactSubmissionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var ResolvedSite $site */
        $site = $request->attributes->get('resolved_site');

        SendMailJob::dispatch(
            type: SendMailJob::TYPE_WEB,
            name: $validated['name'],
            email: $validated['email'],
            message: $validated['message'],
            subject: $validated['subject'] ?? null,
            fileUrl: $validated['file_url'] ?? null,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            tenantId: $site?->tenantId,
            siteId: $site?->siteId,
        );

        return response()->json(['message' => 'Contact request received.'], 202);
    }

    /**
     * Handle contact-form webhook intake.
     * Auth strategy for this route will differ from store() —
     * e.g. HMAC signature verification, API key header, etc.
     */
    public function webhook(WebhookContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var ResolvedSite $site */
        $site = $request->attributes->get('resolved_site');

        SendMailJob::dispatch(
            type: SendMailJob::TYPE_WEBHOOK,
            name: $validated['name'],
            email: $validated['email'],
            message: $validated['message'],
            subject: $validated['subject'] ?? null,
            fileUrl: $validated['file_url'] ?? null,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            tenantId: $site?->tenantId,
            siteId: $site?->siteId,
        );

        return response()->json(['message' => 'Contact request received.'], 202);
    }
}
