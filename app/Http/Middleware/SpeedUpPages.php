<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Small, safe speed-ups applied to every HTML page, so the many pages need no edits:
 *  - images below the first few load only when scrolled near (loading="lazy"),
 *    and are decoded off the main thread (decoding="async");
 *  - video embeds (YouTube, Vimeo, Drive...) load only when visible: a carousel's hidden
 *    slides no longer download their players up front;
 *  - the browser connects early to the CDNs the page uses (preconnect).
 * Script and textarea contents are left untouched.
 */
class SpeedUpPages
{
    /** The first images (logo, hero, first slide) stay eager: they are what the visitor sees first. */
    private const EAGER_IMAGES = 3;

    private const PRECONNECT = [
        'cdn.jsdelivr.net', 'code.jquery.com', 'cdnjs.cloudflare.com', 'unpkg.com', 'cdn.datatables.net',
        'fonts.googleapis.com', 'fonts.gstatic.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $type = (string) $response->headers->get('Content-Type', '');
        if ($response->isRedirection() || ! str_contains($type, 'text/html')
            || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            || $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return $response;
        }
        $html = $response->getContent();
        if (! is_string($html) || $html === '' || stripos($html, '<html') === false) {
            return $response;
        }

        try {
            $response->setContent($this->optimize($html));
        } catch (\Throwable $e) {
            report($e);   // never break a page over an optimisation
        }

        return $response;
    }

    /**
     * Local .css/.js links get ?v=<file date>, so browsers may keep them for days (see .htaccess)
     * and still fetch a new copy the moment the file changes.
     */
    private function versionAssets(string $html): string
    {
        $base = rtrim(url('/'), '/');
        $out = preg_replace_callback('~(<(?:link|script)\b[^>]*?\s(?:href|src)=")([^"?#]+\.(?:css|js))(")~i', function ($m) use ($base) {
            $path = $m[2];
            if (str_starts_with($path, $base.'/')) {
                $rel = substr($path, strlen($base) + 1);
            } elseif (preg_match('#^(https?:)?//#i', $path) || str_contains($path, '<')) {
                return $m[0];
            } else {
                $rel = preg_replace('#^(\./|\.\./)+#', '', ltrim($path, '/'));
                if (! str_starts_with($path, '/') && ! str_starts_with($path, '../')) {
                    // relative to the current page folder: try it as given from the site root
                    $rel = ltrim($path, './');
                }
            }
            $rel = preg_replace('#^academy/assets/#', 'academy-files/assets/', $rel);
            $file = public_path($rel);
            if (! is_file($file)) {
                return $m[0];
            }

            return $m[1].$path.'?v='.filemtime($file).$m[3];
        }, $html);

        return $out ?? $html;
    }

    private function optimize(string $html): string
    {
        $original = $html;
        $html = $this->versionAssets($html);
        // Keep <script> and <textarea> contents exactly as they are
        $kept = [];
        $html = preg_replace_callback('#<(script|textarea)\b[^>]*>.*?</\1>#is', function ($m) use (&$kept) {
            $kept[] = $m[0];

            return "\x01".(count($kept) - 1)."\x01";
        }, $html);
        if ($html === null) {
            throw new \RuntimeException('SpeedUpPages: pattern failed');
        }

        $seen = 0;
        $html = preg_replace_callback('#<img\b[^>]*>#i', function ($m) use (&$seen) {
            $tag = $m[0];
            $seen++;
            if ($seen <= self::EAGER_IMAGES || preg_match('/\sloading\s*=/i', $tag)) {
                return $tag;
            }
            $add = ' loading="lazy"'.(preg_match('/\sdecoding\s*=/i', $tag) ? '' : ' decoding="async"');

            return preg_replace('#\s*/?>$#', $add.'$0', $tag, 1);
        }, $html);

        $html = preg_replace_callback('#<iframe\b[^>]*>#i', function ($m) {
            $tag = $m[0];

            return preg_match('/\sloading\s*=/i', $tag) ? $tag : preg_replace('#>$#', ' loading="lazy">', $tag, 1);
        }, $html);

        // Early connections to the CDNs this page uses
        $hints = '';
        foreach (self::PRECONNECT as $host) {
            if (str_contains($original, '//'.$host.'/') || ($host === 'fonts.gstatic.com' && str_contains($original, '//fonts.googleapis.com/'))) {
                $hints .= '<link rel="preconnect" href="https://'.$host.'"'.($host === 'fonts.gstatic.com' ? ' crossorigin' : '').'>';
            }
        }
        if ($hints !== '') {
            $html = preg_replace('/<head\b[^>]*>/i', '$0'.$hints, $html, 1);
        }

        return preg_replace_callback("/\x01(\d+)\x01/", fn ($m) => $kept[(int) $m[1]], $html);
    }
}
