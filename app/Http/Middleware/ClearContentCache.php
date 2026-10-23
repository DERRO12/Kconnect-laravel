<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The menu, carousel, events, birthdays, spotlights, key contacts and top resources are cached
 * for speed (getCached in app/Support/legacy.php). After any form save, AJAX update or delete
 * the cache is dropped, so admin changes show on the site straight away.
 */
class ClearContentCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD') && $response->getStatusCode() < 400) {
            clearCache();
        }

        return $response;
    }
}
