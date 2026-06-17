<?php

namespace App\Http\Middleware;

use App\Services\Site\SiteResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DraftWebhookSignatureMiddleware
{
    public function __construct(private readonly SiteResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Resolve site context (primarily via X-Key-Id header for webhooks).
            $resolved = $this->resolver->resolve($request);
            $request->attributes->set('resolved_site', $resolved);

            $this->debug('DraftWebhookSignatureMiddleware received request', [
                'path' => $request->path(),
                'resolved_via' => $resolved->resolvedVia,
                'site_id' => $resolved->siteId,
                'auth_mode' => $resolved->authMode->value,
                'x_key_id_present' => $request->header('X-Key-Id') !== null,
                'x_signature_present' => $request->header('X-Signature') !== null,
            ]);

            // TODO: once enforcement is ready:
            // 1. Reject if $resolved->isResolved() === false
            // 2. Look up credential by X-Key-Id
            // 3. Verify X-Timestamp clock skew
            // 4. Verify X-Nonce replay (Redis/DB TTL store)
            // 5. Verify X-Content-SHA256 body hash
            // 6. Verify X-Signature HMAC (constant-time compare)

            $this->debug('DraftWebhookSignatureMiddleware pass-through (draft mode)');

            return $next($request);
        } catch (\Throwable $e) {
            Log::error('DraftWebhookSignatureMiddleware exception', [
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
