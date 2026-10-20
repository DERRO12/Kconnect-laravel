<?php

namespace App\Http\Controllers;

use App\Support\HomeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Employee home page, news and content pages (./, all-news, news-details, gallery, brand-assets, get-asset). */
class HomeController extends Controller
{
    /**
     * ./ (with includes/carousel.php, top-resources, news-events,
     * birthdays-social.php, spotlights.php, key-contacts).
     */
    public function index(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // includes/news-events.php "?action=" requests are the AJAX calls of the news & events block
        // ("View all news", "View all events", event details). The old include printed the page
        // header in front of the JSON, which broke them; answer with the data alone.
        if ($request->query->has('action')) {
            $actionResponse = $this->newsEventsAction($request, (string) $request->query('action'));
            if ($actionResponse !== null) {
                return $actionResponse;
            }
        }

        // includes/carousel.php, includes/top-resources.php
        $carouselInfo = HomeData::carouselInfo();
        $carouselItems = HomeData::carouselItems();
        $topResources = HomeData::topResources();

        $recentNews = HomeData::recentNews();
        $upcomingEvents = HomeData::upcomingEvents();
        $upcomingEventsCount = HomeData::upcomingEventsCount();

        $monthBirthdays = HomeData::employeeBirthdaysForMonth(date('n'));
        $otherBirthdayMonths = count($monthBirthdays) > 0 ? [] : HomeData::employeeBirthdayMonths();

        $spotlights = HomeData::spotlights();
        $keyContacts = HomeData::keyContacts();
        $allSpotlights = HomeData::allSpotlights();

        return view('index', compact(
            'carouselInfo', 'carouselItems', 'topResources',
            'recentNews', 'upcomingEvents', 'upcomingEventsCount', 'monthBirthdays', 'otherBirthdayMonths',
            'spotlights', 'keyContacts', 'allSpotlights'
        ));
    }

    /** The API block at the top of includes/news-events.php. */
    private function newsEventsAction(Request $request, string $action)
    {
        // GET ALL NEWS
        if ($action === 'get_all_news') {
            $allNews = HomeData::allNewsForModal();
            $html = view('includes.news-events-all-news', compact('allNews'))->render();

            return response()->json(['success' => true, 'html' => $html]);
        }

        // GET ALL EVENTS
        if ($action === 'get_all_events') {
            $events = HomeData::allUpcomingEvents();

            return response()->json(['success' => true, 'events' => $events]);
        }

        // GET SINGLE EVENT DETAIL (an HTML fragment for the modal)
        if ($action === 'get_event' && $request->query->has('id')) {
            $eventId = intval($request->query('id'));
            $event = HomeData::eventById($eventId);
            if ($event) {
                return response(view('includes.news-events-event', compact('event'))->render());
            }

            return response('<div class="alert alert-warning text-center py-4"><i class="fas fa-calendar-times fa-3x mb-3"></i><p>Event not found</p></div>');
        }

        // any other ?action= shows the normal page
        return null;
    }

    /** all-news */
    public function allNews(Request $request)
    {
        // Get filter parameters (bound in the SQL; shown and linked as typed)
        $category = is_string($request->query('category')) ? $request->query('category') : '';
        $search = is_string($request->query('search')) ? $request->query('search') : '';
        $categoryRaw = $category;
        $searchRaw = $search;
        $page = $request->query->has('page') && is_numeric($request->query('page')) ? (int) $request->query('page') : 1;
        $limit = 9;

        // Build query
        $where = 'status = 1';
        $bindings = [];
        if (! empty($category)) {
            $where .= ' AND category = ?';
            $bindings[] = $categoryRaw;
        }
        if (! empty($search)) {
            $where .= ' AND (title LIKE ? OR content LIKE ?)';
            $bindings[] = '%'.$searchRaw.'%';
            $bindings[] = '%'.$searchRaw.'%';
        }

        // Get total count
        $total_rows = DB::selectOne("SELECT COUNT(*) as total FROM news_posts WHERE $where", $bindings)['total'];
        $total_pages = (int) ceil($total_rows / $limit);

        // Clamp the page (page=0, negative or past the end used to produce a negative LIMIT offset / an empty page)
        $page = max(1, min($page, max(1, $total_pages)));
        $offset = ($page - 1) * $limit;

        // Get news posts
        $newsRows = DB::select("SELECT id, title, content, category, author_name, author_role, featured_image,
                 comments_count, likes_count, created_at
          FROM news_posts
          WHERE $where
          ORDER BY created_at DESC
          LIMIT $offset, $limit", $bindings);

        // Get categories for filter
        $categories = DB::select('SELECT DISTINCT category, COUNT(*) as count
                     FROM news_posts
                     WHERE status = 1
                     GROUP BY category
                     ORDER BY category');

        return view('all-news', compact('category', 'search', 'page', 'limit', 'total_rows', 'total_pages', 'newsRows', 'categories'));
    }

    /** news-details */
    public function newsDetails(Request $request)
    {
        // Get news ID from URL
        $newsId = $request->query->has('id') ? intval($request->query('id')) : 0;

        if ($newsId <= 0) {
            kc_redirect('./');
        }

        // Fetch the news post first: comments and likes only go to a published post (the old page
        // inserted them for any id and crashed on the foreign key when the post did not exist)
        $news = DB::selectOne('SELECT * FROM news_posts WHERE id = ? AND status = 1', [$newsId]);

        if (! $news) {
            kc_redirect('./');
        }

        // Handle comment submission
        if ($request->isMethod('post') && $request->request->has('submit_comment')) {
            // old: trim(mysqli_real_escape_string(...)) put straight into the SQL text
            $commenterName = is_string($request->post('commenter_name')) ? trim(kc_escape($request->post('commenter_name'))) : '';
            $commentText = is_string($request->post('comment_text')) ? trim(kc_escape($request->post('comment_text'))) : '';

            if (! empty($commenterName) && ! empty($commentText)) {
                $inserted = DB::insert('INSERT INTO news_comments (news_id, commenter_name, comment_text)
                         VALUES (?, ?, ?)', [$newsId, self::sqlLiteralValue($commenterName), self::sqlLiteralValue($commentText)]);
                if ($inserted) {
                    DB::update('UPDATE news_posts SET comments_count = comments_count + 1 WHERE id = ?', [$newsId]);
                    kc_redirect("news-details?id=$newsId&comment=success");
                }
            }
        }

        // Handle like
        $alreadyLiked = null;
        if ($request->query->has('like')) {
            $ip = $request->ip();
            $checkRows = DB::select('SELECT id FROM news_likes WHERE news_id = ? AND ip_address = ?', [$newsId, $ip]);

            if (count($checkRows) == 0) {
                DB::insert('INSERT INTO news_likes (news_id, ip_address) VALUES (?, ?)', [$newsId, $ip]);
                DB::update('UPDATE news_posts SET likes_count = likes_count + 1 WHERE id = ?', [$newsId]);
                kc_redirect("news-details?id=$newsId&liked=1");
            } else {
                $alreadyLiked = true;
            }
        }

        // Fetch additional images
        $images = DB::select('SELECT * FROM news_images WHERE news_id = ? ORDER BY sort_order ASC', [$newsId]);
        $hasAdditionalImages = count($images) > 0;
        $hasFeaturedImage = ! empty($news['featured_image']);
        $totalImages = ($hasFeaturedImage ? 1 : 0) + ($hasAdditionalImages ? count($images) : 0);

        // Get other recent news (excluding current one)
        $otherNewsRows = DB::select('SELECT id, title, featured_image, category, created_at
                   FROM news_posts
                   WHERE status = 1 AND id != ?
                   ORDER BY created_at DESC
                   LIMIT 4', [$newsId]);

        // Fetch comments
        $comments = DB::select('SELECT * FROM news_comments WHERE news_id = ? AND status = 1 ORDER BY created_at DESC', [$newsId]);

        // old: 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        $shareUrl = 'https://'.$request->getHttpHost().kc_request_uri();

        return view('news-details', compact(
            'newsId', 'news', 'alreadyLiked', 'images', 'hasAdditionalImages', 'hasFeaturedImage',
            'totalImages', 'otherNewsRows', 'comments', 'shareUrl'
        ));
    }

    /** The value MySQL stores for the escaped string literal '$escaped' (undoes mysqli_real_escape_string). */
    private static function sqlLiteralValue(string $escaped): string
    {
        return preg_replace_callback('/\\\\(.)/s', function ($m) {
            return ['0' => "\0", 'n' => "\n", 'r' => "\r", 'Z' => "\x1a"][$m[1]] ?? $m[1];
        }, $escaped);
    }

    /** gallery */
    public function gallery(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // ============================================
        // SIMPLE CACHE - the old page kept cache/gallery_page.cache for 60 seconds
        // (cleared by clearCache()). Now Laravel's cache store, same lifetime.
        // ============================================
        $cacheKey = 'kc_gallery_page_'.config('database.connections.'.config('database.default').'.database');
        $cacheTTL = 60; // 1 minute cache

        $cacheValid = false;
        $galleryItems = [];
        $categories = [];

        // Check if cache exists and is fresh
        $cachedData = Cache::get($cacheKey);
        if ($cachedData && ! empty($cachedData['items'])) {
            $galleryItems = $cachedData['items'];
            $categories = $cachedData['categories'];
            $cacheValid = true;
        }

        if (! $cacheValid) {
            // Get all ACTIVE gallery items
            $rows = DB::select('
        SELECT
            gi.id, gi.title, gi.description, gi.thumbnail,
            gi.external_url, gi.source_type, gi.category,
            gi.sort_order, gi.created_at,
            gc.name as category_name,
            gc.description as category_desc
        FROM gallery_items gi
        LEFT JOIN gallery_categories gc ON gc.name = gi.category AND gc.status = 1
        WHERE gi.status = 1
        ORDER BY gi.category, gi.sort_order ASC, gi.created_at DESC
    ');
            $galleryItems = [];
            $categoryCounts = [];

            foreach ($rows as $row) {
                $galleryItems[] = $row;
                $cat = $row['category'];
                if (! isset($categoryCounts[$cat])) {
                    $categoryCounts[$cat] = 0;
                }
                $categoryCounts[$cat]++;
            }

            // Get categories
            $categories = [];
            foreach (DB::select('SELECT * FROM gallery_categories WHERE status = 1 ORDER BY sort_order ASC') as $cat) {
                $cat['item_count'] = $categoryCounts[$cat['name']] ?? 0;
                $categories[] = $cat;
            }

            // Save cache (same JSON round trip as the old file cache)
            Cache::put($cacheKey, json_decode(json_encode([
                'items' => $galleryItems,
                'categories' => $categories,
            ]), true), $cacheTTL);
        }

        // Group by category
        $itemsByCategory = [];
        foreach ($galleryItems as $item) {
            $itemsByCategory[$item['category']][] = $item;
        }

        $sourceIcons = [
            'onedrive' => 'fa-microsoft',
            'googledrive' => 'fa-google-drive',
            'dropbox' => 'fa-dropbox',
            'youtube' => 'fa-youtube',
            'vimeo' => 'fa-vimeo',
            'other' => 'fa-link',
        ];

        return view('gallery', compact('categories', 'itemsByCategory', 'sourceIcons'));
    }

    /** brand-assets */
    public function brandAssets(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // Handle download tracking
        $download = $request->query('download');
        if ($request->query->has('download') && is_numeric($download)) {
            $id = (int) $download;
            DB::update('UPDATE brand_assets SET download_count = download_count + 1 WHERE id = ?', [$id]);

            $asset = DB::selectOne('SELECT file_path FROM brand_assets WHERE id = ?', [$id]);
            if ($asset) {
                kc_redirect(kc_asset($asset['file_path']));
            }
        }

        // Get all active assets
        $allAssets = DB::select('
    SELECT * FROM brand_assets
    WHERE status = 1
    ORDER BY sort_order, created_at DESC
');

        // Company groups from the companies table (HomeData::brandCompanies), "General Assets" last
        $companies = HomeData::brandCompanies();

        // Process assets into companies
        $companyAssets = [];
        $allAssetData = [];

        foreach ($allAssets as $asset) {
            $allAssetData[] = $asset;

            $assigned = false;
            foreach ($companies as $ckey => $company) {
                if ($ckey === 'general') {
                    continue;
                }
                foreach ($company['keywords'] as $keyword) {
                    if (stripos($asset['title'], $keyword) !== false ||
                        stripos($asset['description'] ?? '', $keyword) !== false) {
                        $companyAssets[$ckey][] = $asset;
                        $assigned = true;
                        break 2;
                    }
                }
            }
            if (! $assigned) {
                $companyAssets['general'][] = $asset;
            }
        }

        $totalAssets = count($allAssetData);
        $totalDownloads = array_sum(array_column($allAssetData, 'download_count'));

        return view('brand-assets', compact('companies', 'companyAssets', 'totalAssets', 'totalDownloads'));
    }

    /** get-asset (JSON body, no Content-Type header: text/html like before) */
    public function getAsset(Request $request)
    {
        $id = $request->query('id');
        if ($request->query->has('id') && is_numeric($id)) {
            $id = (int) $id;

            $asset = DB::selectOne('SELECT * FROM brand_assets WHERE id = ? AND status = 1', [$id]);

            if ($asset) {
                // Format file size
                $fileSize = $asset['file_size'] ? round($asset['file_size'] / 1024, 2).' KB' : 'Unknown';
                if ($asset['file_size'] > 1048576) {
                    $fileSize = round($asset['file_size'] / 1048576, 2).' MB';
                }

                return response(json_encode([
                    'success' => true,
                    'id' => $asset['id'],
                    'title' => $asset['title'],
                    'description' => $asset['description'],
                    'file_path' => kc_asset($asset['file_path']),
                    'file_size' => $fileSize,
                    'file_type' => strtolower(pathinfo($asset['file_path'], PATHINFO_EXTENSION)),
                    'download_count' => $asset['download_count'],
                    'created_at' => $asset['created_at'],
                ]));
            }

            return response(json_encode(['success' => false, 'message' => 'Asset not found']));
        }

        return response(json_encode(['success' => false, 'message' => 'Invalid request']));
    }
}
