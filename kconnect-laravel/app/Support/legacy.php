<?php

/*
|--------------------------------------------------------------------------
| K-Connect shared helpers (ported from the old config.php)
|--------------------------------------------------------------------------
| The old site defined these functions in config.php and every page used them.
| They keep the same names and behaviour so the pages read the same, but they
| now run on Laravel's database, session, cache and response layers.
*/

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// ---------------------------------------------------------------------------
// Constants the old pages used
// ---------------------------------------------------------------------------
defined('SITE_NAME') || define('SITE_NAME', 'K-Connect');
defined('SITE_URL') || define('SITE_URL', 'https://kconnect.kibokogroup.com');
defined('CACHE_ENABLED') || define('CACHE_ENABLED', filter_var(env('KC_CACHE_ENABLED', true), FILTER_VALIDATE_BOOL));
defined('CACHE_LIFETIME') || define('CACHE_LIFETIME', 300);        // 5 minutes default
defined('CACHE_LIFETIME_LONG') || define('CACHE_LIFETIME_LONG', 3600); // 1 hour for static data

if (! function_exists('kc_upload_path')) {
    /** Absolute path of the public uploads folder (old UPLOAD_PATH = __DIR__.'/uploads/'). */
    function kc_upload_path(string $sub = ''): string
    {
        return public_path('uploads/'.ltrim($sub, '/'));
    }
}

// ---------------------------------------------------------------------------
// Request / response helpers that replace header('Location'), exit and $_SERVER['PHP_SELF']
// ---------------------------------------------------------------------------

if (! function_exists('kc_redirect')) {
    /**
     * Old redirect($url): header("Location: $url"); exit();
     * The Location header is sent exactly as given (relative URLs stay relative, so
     * 'login.php' from /admin/x.php still goes to /admin/login.php). Stops the request.
     */
    function kc_redirect(string $url, int $status = 302): never
    {
        throw new HttpResponseException(kc_redirect_response($url, $status));
    }
}

if (! function_exists('kc_redirect_response')) {
    /** Same as kc_redirect() but returns the response instead of stopping. */
    function kc_redirect_response(string $url, int $status = 302): \Illuminate\Http\Response
    {
        return response('', $status)->header('Location', $url);
    }
}

if (! function_exists('kc_exit')) {
    /**
     * Old exit / die('text'): stop the request and send $content (string, array => JSON, or a Response).
     */
    function kc_exit(mixed $content = '', int $status = 200, array $headers = []): never
    {
        if ($content instanceof \Symfony\Component\HttpFoundation\Response) {
            throw new HttpResponseException($content);
        }
        if (is_array($content)) {
            throw new HttpResponseException(kc_json($content, $status, $headers));
        }
        throw new HttpResponseException(response((string) $content, $status, $headers));
    }
}

if (! function_exists('kc_json')) {
    /**
     * Old: header('Content-Type: application/json'); echo json_encode($data);
     * Encodes with the same json_encode() flags the old pages used (default: none).
     */
    function kc_json(mixed $data, int $status = 200, array $headers = [], int $flags = 0): \Illuminate\Http\Response
    {
        return response(json_encode($data, $flags), $status, $headers + ['Content-Type' => 'application/json']);
    }
}

if (! function_exists('kc_self')) {
    /** Old $_SERVER['PHP_SELF'] (e.g. "/admin/dashboard.php"). */
    function kc_self(): string
    {
        $path = request()->path();

        return $path === '/' ? '/index.php' : '/'.$path;
    }
}

if (! function_exists('kc_request_uri')) {
    /** Old $_SERVER['REQUEST_URI'] (path + query string). */
    function kc_request_uri(): string
    {
        return request()->getRequestUri();
    }
}

// ---------------------------------------------------------------------------
// Session / login checks (same session keys as before)
// ---------------------------------------------------------------------------

if (! function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return session()->has('employee_id');
    }
}

if (! function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return session()->has('employee_role') && session('employee_role') === 'admin';
    }
}

if (! function_exists('isAdminLoggedIn')) {
    function isAdminLoggedIn(): bool
    {
        return session()->has('admin_id');
    }
}

// ---------------------------------------------------------------------------
// Input / password helpers
// ---------------------------------------------------------------------------

if (! function_exists('sanitize')) {
    /**
     * Old: mysqli_real_escape_string($conn, htmlspecialchars(trim($input)))
     * The value is now used with query bindings, so only the htmlspecialchars(trim()) part
     * is kept: what is stored in the database is byte-for-byte what the old site stored.
     */
    function sanitize($input): string
    {
        return htmlspecialchars(trim((string) $input));
    }
}

if (! function_exists('kc_escape')) {
    /**
     * Old mysqli_real_escape_string($conn, $v) for the rare places where a value is still
     * put inside a SQL string. Prefer bindings.
     */
    function kc_escape($value): string
    {
        $quoted = DB::connection()->getPdo()->quote((string) $value);

        return substr($quoted, 1, -1);
    }
}

if (! function_exists('hashPassword')) {
    function hashPassword($password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

if (! function_exists('verifyPassword')) {
    function verifyPassword($password, $hash): bool
    {
        return password_verify((string) $password, (string) $hash);
    }
}

if (! function_exists('generateEmployeeCode')) {
    function generateEmployeeCode($prefix = 'KEL'): string
    {
        $row = DB::selectOne('SELECT COUNT(*) as total FROM employees');
        $count = $row['total'] + 1;

        return $prefix.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

// ---------------------------------------------------------------------------
// Cache (old file cache in /cache, same keys and lifetimes, now Laravel's cache store)
// ---------------------------------------------------------------------------

if (! function_exists('getCached')) {
    function getCached($key, $callback, $ttl = CACHE_LIFETIME)
    {
        if (! CACHE_ENABLED) {
            return $callback();
        }
        $cacheKey = 'kc_'.md5($key);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $data = $callback();
        if ($data !== null) {
            Cache::put($cacheKey, $data, $ttl);
        }

        return $data;
    }
}

if (! function_exists('clearCache')) {
    function clearCache($pattern = null): void
    {
        if ($pattern === null) {
            Cache::flush();

            return;
        }
        Cache::forget('kc_'.md5($pattern));
    }
}

// ---------------------------------------------------------------------------
// Cached content queries used by the home page partials
// ---------------------------------------------------------------------------

if (! function_exists('getNavigationLinks')) {
    function getNavigationLinks(): array
    {
        return getCached('navigation_links', function () {
            return DB::select('SELECT * FROM navigation_links WHERE status = 1 ORDER BY sort_order ASC');
        }, CACHE_LIFETIME_LONG);
    }
}

if (! function_exists('getMenu')) {
    /** Echoes the navbar <li> items, like before. */
    function getMenu($parent_id = null, $level = 0): void
    {
        $allLinks = getNavigationLinks();
        $menu = [];
        foreach ($allLinks as $link) {
            $parent = $link['parent_id'] ?? null;
            if (! isset($menu[$parent])) {
                $menu[$parent] = [];
            }
            $menu[$parent][] = $link;
        }
        renderMenu($menu, $parent_id, $level);
    }
}

if (! function_exists('renderMenu')) {
    function renderMenu($menu, $parent_id = null, $level = 0): void
    {
        $items = $menu[$parent_id] ?? [];
        foreach ($items as $item) {
            $children = $menu[$item['id']] ?? [];
            $hasChildren = ! empty($children);

            if ($level == 0) {
                $liClass = $hasChildren ? 'nav-item dropdown' : 'nav-item';
                $linkClass = $hasChildren ? 'nav-link dropdown-toggle' : 'nav-link';
            } else {
                $liClass = $hasChildren ? 'dropdown-submenu' : '';
                $linkClass = $hasChildren ? 'dropdown-item dropdown-toggle' : 'dropdown-item';
            }

            if ($item['is_label']) {
                if ($level == 0) {
                    echo '<li class="nav-item"><span class="nav-link disabled">'.htmlspecialchars($item['title']).'</span></li>';
                } else {
                    echo '<li><h6 class="dropdown-header">'.htmlspecialchars($item['title']).'</h6></li>';
                    if ($hasChildren) {
                        echo '<li><ul class="dropdown-submenu-list">';
                        renderMenu($menu, $item['id'], $level + 1);
                        echo '</ul></li>';
                    }
                }
            } else {
                $url = ! empty($item['url']) ? htmlspecialchars($item['url']) : '#';
                $target = ! empty($item['target']) ? ' target="'.$item['target'].'"' : '';

                if ($level == 0) {
                    echo '<li class="'.$liClass.'">';
                    if ($hasChildren) {
                        echo '<a class="'.$linkClass.'" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                        echo htmlspecialchars($item['title']);
                        echo '</a>';
                        echo '<ul class="dropdown-menu">';
                        renderMenu($menu, $item['id'], $level + 1);
                        echo '</ul>';
                    } else {
                        echo '<a class="'.$linkClass.'" href="'.$url.'"'.$target.'>'.htmlspecialchars($item['title']).'</a>';
                    }
                    echo '</li>';
                } else {
                    if ($hasChildren) {
                        echo '<li class="dropdown-submenu">';
                        echo '<a class="'.$linkClass.'" href="#">'.htmlspecialchars($item['title']).'</a>';
                        echo '<ul class="dropdown-menu">';
                        renderMenu($menu, $item['id'], $level + 1);
                        echo '</ul>';
                        echo '</li>';
                    } else {
                        echo '<li><a class="dropdown-item" href="'.$url.'"'.$target.'>'.htmlspecialchars($item['title']).'</a></li>';
                    }
                }
            }
        }
    }
}

if (! function_exists('getNewsPosts')) {
    function getNewsPosts($limit = 5, $category = null): array
    {
        $cacheKey = 'news_'.$limit.'_'.($category ?: 'all');

        return getCached($cacheKey, function () use ($limit, $category) {
            $sql = 'SELECT id, title, content, category, author_name, featured_image,
                       comments_count, likes_count, created_at
                FROM news_posts
                WHERE status = 1';
            $bindings = [];
            if ($category) {
                $sql .= ' AND category = ?';
                $bindings[] = $category;
            }
            $sql .= ' ORDER BY created_at DESC LIMIT '.intval($limit);

            return DB::select($sql, $bindings);
        }, CACHE_LIFETIME);
    }
}

if (! function_exists('getEvents')) {
    function getEvents($limit = 5): array
    {
        return getCached('events_'.$limit, function () use ($limit) {
            return DB::select('SELECT id, title, event_date, event_time, location, category, description, rsvp_required, rsvp_url
                FROM event_items
                WHERE status = 1 AND event_date >= CURDATE()
                ORDER BY event_date ASC, event_time ASC
                LIMIT '.intval($limit));
        }, CACHE_LIFETIME);
    }
}

if (! function_exists('getBirthdays')) {
    function getBirthdays($month = null): array
    {
        if ($month === null) {
            $month = date('n');
        }

        return getCached('birthdays_'.$month, function () use ($month) {
            return DB::select('SELECT *, DAY(birth_date) as birth_day
                FROM birthdays
                WHERE status = 1 AND MONTH(birth_date) = '.intval($month).'
                ORDER BY DAY(birth_date) ASC');
        }, CACHE_LIFETIME_LONG);
    }
}

if (! function_exists('getSpotlights')) {
    function getSpotlights($limit = 6): array
    {
        return getCached('spotlights_'.$limit, function () use ($limit) {
            return DB::select('SELECT * FROM employee_spotlights
                WHERE status = 1
                ORDER BY sort_order, created_at DESC
                LIMIT '.intval($limit));
        }, CACHE_LIFETIME);
    }
}

if (! function_exists('getKeyContacts')) {
    function getKeyContacts(): array
    {
        return getCached('key_contacts', fn () => DB::select('SELECT * FROM key_contacts WHERE status = 1 ORDER BY category, sort_order ASC'), CACHE_LIFETIME_LONG);
    }
}

if (! function_exists('getTopResources')) {
    function getTopResources(): array
    {
        return getCached('top_resources', fn () => DB::select('SELECT * FROM top_resources WHERE status = 1 ORDER BY category, sort_order ASC'), CACHE_LIFETIME_LONG);
    }
}

if (! function_exists('getCarouselItems')) {
    function getCarouselItems(): array
    {
        return getCached('carousel_items', fn () => DB::select('SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC'), CACHE_LIFETIME_LONG);
    }
}

if (! function_exists('getCarouselInfo')) {
    function getCarouselInfo()
    {
        $items = getCarouselItems();

        return ! empty($items) ? $items[0] : null;
    }
}

// ---------------------------------------------------------------------------
// Files
// ---------------------------------------------------------------------------

if (! function_exists('cachedFileExists')) {
    /** Relative paths are resolved against public/, like the old site's document root. */
    function cachedFileExists($path): bool
    {
        static $fileCache = [];
        $key = md5($path);
        if (! isset($fileCache[$key])) {
            $fileCache[$key] = file_exists(kc_public_file($path));
        }

        return $fileCache[$key];
    }
}

if (! function_exists('kc_public_file')) {
    /**
     * Maps a path the old code used relative to its own folder to the real file under public/.
     * kc_public_file('uploads/news/a.jpg') from a root page, kc_public_file('../uploads/a.jpg', 'admin')
     * from an admin page. Absolute paths are returned unchanged.
     */
    function kc_public_file(string $path, string $scriptDir = ''): string
    {
        if ($path !== '' && ($path[0] === '/' || preg_match('#^[A-Za-z]:[\\\\/]#', $path))) {
            return $path;
        }
        $parts = [];
        foreach (explode('/', trim($scriptDir, '/').'/'.$path) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                array_pop($parts);

                continue;
            }
            $parts[] = $seg;
        }

        return public_path(implode('/', $parts)).(str_ends_with($path, '/') ? '/' : '');
    }
}

if (! function_exists('newsHasImage')) {
    function newsHasImage($image): bool
    {
        if (empty($image)) {
            return false;
        }

        return cachedFileExists('uploads/news/'.$image);
    }
}

if (! function_exists('ensureUploadDirectories')) {
    function ensureUploadDirectories(): void
    {
        foreach ([kc_upload_path(), kc_upload_path('employees/'), kc_upload_path('news/')] as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }
}
