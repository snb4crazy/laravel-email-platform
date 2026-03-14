<?php

namespace App\Http\Middleware;

use App\Services\Site\SiteResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftContactAuthMiddleware
{
    public function __construct(private readonly SiteResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Resolve site context and attach it to the request for downstream use.
        $resolved = $this->resolver->resolve($request);
        $request->attributes->set('resolved_site', $resolved);

        // TODO: once enforcement is ready, check $resolved->authMode here and
        // verify captcha token / API key / origin against $resolved->siteId.

        return $next($request);
    }
}
