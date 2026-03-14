<?php

namespace App\Http\Middleware;

use App\Services\Site\SiteResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftWebhookSignatureMiddleware
{
    public function __construct(private readonly SiteResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Resolve site context (primarily via X-Key-Id header for webhooks).
        $resolved = $this->resolver->resolve($request);
        $request->attributes->set('resolved_site', $resolved);

        // TODO: once enforcement is ready:
        // 1. Reject if $resolved->isResolved() === false
        // 2. Look up credential by X-Key-Id
        // 3. Verify X-Timestamp clock skew
        // 4. Verify X-Nonce replay (Redis/DB TTL store)
        // 5. Verify X-Content-SHA256 body hash
        // 6. Verify X-Signature HMAC (constant-time compare)

        return $next($request);
    }
}
