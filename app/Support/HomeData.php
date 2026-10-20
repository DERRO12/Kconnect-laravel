<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Queries the old home page includes (includes/*.php) ran inline, same SQL.
 * Rows are associative arrays of strings, like mysqli_fetch_assoc().
 */
class HomeData
{
    /** includes/carousel.php: info card (first active slide) or null */
    public static function carouselInfo(): ?array
    {
        return DB::selectOne('SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC LIMIT 1') ?: null;
    }

    /** includes/carousel.php: all active slides */
    public static function carouselItems(): array
    {
        return DB::select('SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC');
    }

    /** includes/top-resources.php */
    public static function topResources(): array
    {
        return DB::select('SELECT * FROM top_resources WHERE status = 1 ORDER BY category, sort_order ASC');
    }

    /** includes/news-events.php: two most recent posts */
    public static function recentNews(): array
    {
        return DB::select('SELECT id, title, content, category, author_name, featured_image,
                                           comments_count, likes_count, created_at
                                    FROM news_posts
                                    WHERE status = 1
                                    ORDER BY created_at DESC
                                    LIMIT 2');
    }

    /** includes/news-events.php: next 5 events */
    public static function upcomingEvents(): array
    {
        return DB::select('SELECT id, title, event_date, event_time, location, category
                                       FROM event_items
                                       WHERE status = 1 AND event_date >= CURDATE()
                                       ORDER BY event_date ASC, event_time ASC
                                       LIMIT 5');
    }

    /** includes/news-events.php: count of upcoming events (row with 'total') */
    public static function upcomingEventsCount(): ?array
    {
        return DB::selectOne('SELECT COUNT(*) as total FROM event_items WHERE status = 1 AND event_date >= CURDATE()') ?: null;
    }

    /** includes/news-events.php ?action=get_all_news */
    public static function allNewsForModal(): array
    {
        return DB::select('SELECT id, title, content, category, author_name, author_role, featured_image,
                         comments_count, likes_count, created_at
                  FROM news_posts
                  WHERE status = 1
                  ORDER BY created_at DESC');
    }

    /** includes/news-events.php ?action=get_all_events */
    public static function allUpcomingEvents(): array
    {
        return DB::select('SELECT id, title, event_date, event_time, location, category
                  FROM event_items
                  WHERE status = 1 AND event_date >= CURDATE()
                  ORDER BY event_date ASC, event_time ASC');
    }

    /** includes/news-events.php ?action=get_event */
    public static function eventById(int $eventId): ?array
    {
        return DB::selectOne('SELECT * FROM event_items WHERE id = ?', [$eventId]) ?: null;
    }

    /** includes/birthdays-social.php: this month's birthdays from employees */
    public static function employeeBirthdaysForMonth($currentMonth): array
    {
        return DB::select('SELECT
                                    id,
                                    full_name,
                                    photo,
                                    department,
                                    birth_date,
                                    DAY(birth_date) as birth_day
                                FROM employees
                                WHERE status = 1
                                AND birth_date IS NOT NULL
                                AND MONTH(birth_date) = ?
                                ORDER BY birth_day ASC', [$currentMonth]);
    }

    /** includes/birthdays-social.php: months that have birthdays (shown when this month has none) */
    public static function employeeBirthdayMonths(): array
    {
        return DB::select('SELECT MONTH(birth_date) as month, COUNT(*) as count
                                    FROM employees WHERE status = 1 AND birth_date IS NOT NULL
                                    GROUP BY MONTH(birth_date) ORDER BY month ASC');
    }

    /** includes/spotlights.php */
    public static function spotlights(): array
    {
        return DB::select('SELECT * FROM employee_spotlights
                       WHERE status = 1 ORDER BY sort_order, created_at DESC LIMIT 6');
    }

    /** ./ "All spotlights" modal and ajax/get-all-spotlights */
    public static function allSpotlights(): array
    {
        return DB::select('SELECT * FROM employee_spotlights WHERE status = 1 ORDER BY sort_order, created_at DESC');
    }

    /** includes/key-contacts.php */
    public static function keyContacts(): array
    {
        return DB::select('SELECT * FROM key_contacts WHERE status = 1 ORDER BY category, sort_order ASC');
    }

    /** all-news / news-details getCategoryClass() */
    public static function categoryClass($category): string
    {
        switch ($category) {
            case 'Announcements': return 'primary';
            case 'Events': return 'success';
            case 'People': return 'warning';
            case 'Milestones': return 'info';
            default: return 'secondary';
        }
    }

    /**
     * partials/footer: social icons (social_links, managed on admin/manage-birthdays), quick links
     * (quick_links) and resources (top_resources). Loaded once per request; a failing query only
     * empties its list so the footer never breaks a page.
     */
    public static function footer(): array
    {
        static $data = null;
        if ($data !== null) {
            return $data;
        }

        $safe = function (string $sql): array {
            try {
                return DB::select($sql);
            } catch (\Throwable $e) {
                report($e);

                return [];
            }
        };

        // admin/manage-birthdays stores the social fields through sanitize() (HTML-escaped): undo that
        // so the link and name are escaped once, in the view
        $socialLinks = array_map(fn ($row) => array_map(fn ($v) => is_string($v) ? htmlspecialchars_decode($v) : $v, $row),
            $safe("SELECT platform, icon, url FROM social_links WHERE status = 1 AND url IS NOT NULL AND url != '' ORDER BY sort_order ASC"));

        return $data = [
            'socialLinks' => $socialLinks,
            'quickLinks' => $safe('SELECT title, icon, url FROM quick_links WHERE status = 1 ORDER BY sort_order ASC LIMIT 6'),
            'resources' => $safe('SELECT title, icon, url, target FROM top_resources WHERE status = 1 ORDER BY sort_order ASC LIMIT 4'),
            // "Contact IT : <ext>": the first active IT key contact that has an extension or phone
            'itContact' => $safe("SELECT name, phone, extension FROM key_contacts
                                  WHERE status = 1 AND category = 'IT'
                                    AND ((extension IS NOT NULL AND extension != '') OR (phone IS NOT NULL AND phone != ''))
                                  ORDER BY sort_order ASC LIMIT 1")[0] ?? null,
        ];
    }

    /** Font Awesome classes for a stored icon ("fa-twitter", "fab fa-twitter", "fas fa-link"). */
    public static function iconClass(?string $icon, string $defaultStyle = 'fas'): string
    {
        $icon = trim((string) $icon);
        if ($icon === '') {
            return $defaultStyle.' fa-link';
        }

        return preg_match('/(^|\s)fa[bsrl]?(\s|$)|fa-(brands|solid|regular)/', $icon) ? $icon : $defaultStyle.' '.$icon;
    }

    /** Look of the group companies the brand pages knew before (matched to `companies` rows by name/code). */
    private const BRAND_PRESETS = [
        'kiboko_enterprises' => [
            'name' => 'Kiboko Enterprises Ltd', 'subtitle' => 'Flagship operation', 'icon' => 'fa-building', 'color' => '#009ada',
            'keywords' => ['kiboko enterprise', 'kel', 'kiboko group', 'kiboko enterprises'],
        ],
        'kibocell' => [
            'name' => 'Kibocell', 'subtitle' => 'Mobile division', 'icon' => 'fa-mobile-alt', 'color' => '#76bc21',
            'keywords' => ['kibocell', 'kibocel'],
        ],
        'kfs' => [
            'name' => 'Kiboko Financial Services', 'subtitle' => 'Financial services arm', 'icon' => 'fa-chart-line', 'color' => '#6f42c1',
            'keywords' => ['kfs', 'kiboko financial', 'financial services'],
        ],
        'kibs' => [
            'name' => 'KIBS', 'subtitle' => 'Business solutions', 'icon' => 'fa-store', 'color' => '#f59e0b',
            'keywords' => ['kibs'],
        ],
    ];

    /**
     * brand-assets / admin/manage-brand-assets company groups, from the `companies` table
     * (active rows, by sort_order). Each company gets keywords from its name and code (assets whose
     * title or description mention them are grouped under it); a company that matches one of the
     * known group companies keeps that one's key, icon and colour. "General Assets" is always last.
     * If the table has no active rows the known group companies are used.
     *
     * @return array<string, array{name: string, subtitle: string, icon: string, color: string, keywords: string[]}>
     */
    public static function brandCompanies(): array
    {
        static $companies = null;
        if ($companies !== null) {
            return $companies;
        }

        try {
            $rows = DB::select('SELECT id, name, code, description FROM companies WHERE status = 1 ORDER BY sort_order ASC, name ASC');
        } catch (\Throwable $e) {
            report($e);
            $rows = [];
        }

        $companies = [];
        if (count($rows) === 0) {
            $companies = self::BRAND_PRESETS;
        } else {
            $palette = [['fa-building', '#009ada'], ['fa-briefcase', '#76bc21'], ['fa-chart-line', '#6f42c1'], ['fa-store', '#f59e0b'], ['fa-industry', '#0ea5e9'], ['fa-globe-africa', '#14b8a6'], ['fa-handshake', '#ec4899'], ['fa-truck', '#ef4444']];
            $i = 0;
            foreach ($rows as $row) {
                $name = trim((string) $row['name']);
                $code = trim((string) ($row['code'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $keywords = array_values(array_unique(array_filter([strtolower($name), strtolower($code)], fn ($k) => strlen($k) >= 2)));

                // A known group company? (its keyword appears in the name/code, or the other way round)
                $presetKey = null;
                foreach (self::BRAND_PRESETS as $pkey => $preset) {
                    foreach (array_merge([strtolower($preset['name'])], $preset['keywords']) as $kw) {
                        if (in_array($kw, $keywords, true) || str_contains(strtolower($name), $kw) && strlen($kw) > 3) {
                            $presetKey = $pkey;
                            break 2;
                        }
                    }
                }

                [$icon, $color] = $palette[$i++ % count($palette)];
                $key = $presetKey && ! isset($companies[$presetKey]) ? $presetKey : 'company_'.$row['id'];
                $preset = $presetKey ? self::BRAND_PRESETS[$presetKey] : null;
                $description = trim(strip_tags((string) ($row['description'] ?? '')));

                $companies[$key] = [
                    'name' => $name,
                    'subtitle' => $preset['subtitle'] ?? ($description !== '' ? \Illuminate\Support\Str::limit($description, 40) : ($code !== '' ? $code : 'Group company')),
                    'icon' => $preset['icon'] ?? $icon,
                    'color' => $preset['color'] ?? $color,
                    'keywords' => array_values(array_unique(array_merge($keywords, $preset['keywords'] ?? []))),
                ];
            }
        }

        $companies['general'] = [
            'name' => 'General Assets', 'subtitle' => 'Shared resources', 'icon' => 'fa-folder', 'color' => '#64748b', 'keywords' => [],
        ];

        return $companies;
    }
}
