<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'url', 'max:2048'],
        ]);

        SendMailJob::dispatch(
            type: SendMailJob::TYPE_WEB,
            name: $validated['name'],
            email: $validated['email'],
            message: $validated['message'],
            subject: $validated['subject'] ?? null,
            fileUrl: $validated['file_url'] ?? null,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => 'Contact request received.',
        ], 202);
    }

    /**
     * Handle contact-form webhook intake.
     * Auth strategy for this route will differ from store() —
     * e.g. HMAC signature verification, API key header, etc.
     */
    public function webhook(Request $request): JsonResponse
    {
        // TODO: add webhook auth/signature verification here

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'subject' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'url', 'max:2048'],
        ]);

        SendMailJob::dispatch(
            type: SendMailJob::TYPE_WEBHOOK,
            name: $validated['name'],
            email: $validated['email'],
            message: $validated['message'],
            subject: $validated['subject'] ?? null,
            fileUrl: $validated['file_url'] ?? null,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => 'Contact request received.',
        ], 202);
    }
}
