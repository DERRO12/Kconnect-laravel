<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Old addresses such as /employee-directory or /admin/dashboard.php.
 *
 * A browser visit (GET/HEAD) is sent to the clean address with a permanent redirect, keeping
 * the query string, so bookmarks, links stored in the database and emails already sent keep
 * working. Other requests (form posts, AJAX) are answered directly, because a redirect would
 * drop the posted data.
 */
class RedirectLegacyPhpUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $clean = preg_replace('/\.php$/', '', $request->getPathInfo());
            $query = $request->getQueryString();

            return redirect()->to(url($clean).($query !== null ? '?'.$query : ''), 301);
        }

        return $next($request);
    }
}
