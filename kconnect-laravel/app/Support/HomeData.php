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

    /** index.php "All spotlights" modal and ajax/get-all-spotlights.php */
    public static function allSpotlights(): array
    {
        return DB::select('SELECT * FROM employee_spotlights WHERE status = 1 ORDER BY sort_order, created_at DESC');
    }

    /** includes/key-contacts.php */
    public static function keyContacts(): array
    {
        return DB::select('SELECT * FROM key_contacts WHERE status = 1 ORDER BY category, sort_order ASC');
    }

    /** all-news.php / news-details.php getCategoryClass() */
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
}
