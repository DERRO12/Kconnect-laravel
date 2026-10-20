<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminContentHelpers as H;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin content pages: news, events, birthdays, carousel, spotlights, gallery, brand assets.
 * (was admin/news-*.php, admin/event-*.php, admin/manage-*.php, admin/normalize-birthdays.php)
 *
 * The old pages ran mysqli in exception mode: a failing query ended the script with an uncaught
 * "Fatal error" (HTTP 200, nothing else printed; every query runs before any HTML is output).
 * callAction() reproduces that by turning a QueryException into an empty 200 response.
 */
class ContentController extends Controller
{
    public function callAction($method, $parameters)
    {
        try {
            return $this->{$method}(...array_values($parameters));
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return response('', 200);
        }
    }

    /** admin/news-add.php */
    public function newsAdd(Request $request)
    {
        // Check login
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        $error = null;
        $success = null;

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = (string) $request->input('title');
            $content = (string) $request->input('content');
            $category = (string) $request->input('category');
            $author_name = (string) $request->input('author_name');
            $author_role = (string) $request->input('author_role');
            $status = $request->has('status') ? 1 : 0;

            // Handle featured image upload
            $featured_image = '';
            $target_dir = null;
            $file = $request->file('featured_image');
            if ($file && $file->isValid()) {
                $target_dir = '../uploads/news/';

                if (! file_exists(kc_public_file($target_dir, 'admin'))) {
                    mkdir(kc_public_file($target_dir, 'admin'), 0777, true);
                }

                $imageFileType = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $featured_image = time().'_'.uniqid().'.'.$imageFileType;

                $check = @getimagesize($file->getRealPath());
                if ($check !== false) {
                    if (! H::move($file, kc_public_file($target_dir.$featured_image, 'admin'))) {
                        $error = 'Sorry, there was an error uploading the featured image.';
                    }
                } else {
                    $error = 'Featured image file is not a valid image.';
                    $featured_image = '';
                }
            }

            // Insert news post
            DB::insert(
                'INSERT INTO news_posts (title, content, category, author_name, author_role, featured_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $content, $category, $author_name, $author_role, $featured_image ? $featured_image : null, (string) $status]
            );
            $news_id = DB::getPdo()->lastInsertId();

            // Handle multiple additional images
            $files = H::fileList($request, 'additional_images');
            if ($files !== null) {
                $totalFiles = count($files);
                $sort_order = 0;
                $captions = $request->input('image_captions');

                for ($i = 0; $i < $totalFiles; $i++) {
                    $f = $files[$i] ?? null;
                    if ($f && $f->isValid()) {
                        $imageFileType = strtolower(pathinfo($f->getClientOriginalName(), PATHINFO_EXTENSION));

                        // Validate file type
                        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (! in_array($imageFileType, $allowedTypes)) {
                            continue;
                        }

                        $imageName = time().'_'.uniqid().'_'.$i.'.'.$imageFileType;
                        // (old code: $target_dir was only set when a featured image was uploaded)
                        $target_file = ($target_dir ?? '').$imageName;

                        $check = @getimagesize($f->getRealPath());
                        if ($check !== false) {
                            if (H::move($f, kc_public_file($target_file, 'admin'))) {
                                $caption = isset($captions[$i]) ? (string) $captions[$i] : '';
                                DB::insert(
                                    'INSERT INTO news_images (news_id, image_path, sort_order, caption) VALUES (?, ?, ?, ?)',
                                    [$news_id, $imageName, (string) $sort_order, $caption ? $caption : null]
                                );
                                $sort_order++;
                            }
                        }
                    }
                }
            }

            kc_redirect('news-events.php?success=added');
        }

        return view('admin.news-add', compact('error', 'success'));
    }

    /** admin/news-edit.php */
    public function newsEdit(Request $request)
    {
        // Check login
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        // Get news ID from URL
        $newsId = $request->query('id') !== null ? intval($request->query('id')) : 0;

        if ($newsId <= 0) {
            kc_redirect('news-events.php');
        }

        // Fetch existing news data
        $newsData = DB::selectOne('SELECT * FROM news_posts WHERE id = ?', [$newsId]);

        if (! $newsData) {
            kc_redirect('news-events.php');
        }

        $error = null;
        $success = null;

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = (string) $request->input('title');
            $content = (string) $request->input('content');
            $category = (string) $request->input('category');
            $author_name = (string) $request->input('author_name');
            $author_role = (string) $request->input('author_role');
            $status = $request->has('status') ? 1 : 0;

            $target_dir = '../uploads/news/';
            $dir = kc_public_file($target_dir, 'admin');
            if (! file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            // Handle featured image upload
            $featured_image = $newsData['featured_image']; // Keep existing by default
            $file = $request->file('featured_image');

            // Check if we should remove the featured image
            if ($request->has('remove_featured') && $request->input('remove_featured') == '1') {
                // Delete old file
                if (! empty($newsData['featured_image']) && file_exists($dir.$newsData['featured_image'])) {
                    unlink($dir.$newsData['featured_image']);
                }
                $featured_image = '';
            }
            // Check if new featured image is uploaded
            elseif ($file && $file->isValid()) {
                // Delete old file if exists
                if (! empty($newsData['featured_image']) && file_exists($dir.$newsData['featured_image'])) {
                    unlink($dir.$newsData['featured_image']);
                }

                $imageFileType = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $featured_image = time().'_'.uniqid().'_main.'.$imageFileType;

                $check = @getimagesize($file->getRealPath());
                if ($check !== false) {
                    if (H::move($file, $dir.$featured_image)) {
                        $success = 'Featured image updated successfully!';
                    } else {
                        $error = 'Sorry, there was an error uploading your file.';
                        $featured_image = $newsData['featured_image']; // Revert to old
                    }
                } else {
                    $error = 'Featured image file is not a valid image.';
                    $featured_image = $newsData['featured_image']; // Revert to old
                }
            }

            // Update news post
            DB::update(
                'UPDATE news_posts SET title = ?, content = ?, category = ?, author_name = ?, author_role = ?, featured_image = ?, status = ? WHERE id = ?',
                [$title, $content, $category, $author_name, $author_role, $featured_image ? $featured_image : null, (string) $status, $newsId]
            );

            // Handle deletion of existing additional images
            $deleteImages = $request->input('delete_images');
            if (is_array($deleteImages)) {
                foreach ($deleteImages as $deleteImgId) {
                    $deleteImgId = intval($deleteImgId);
                    // Get image path first
                    $imgRow = DB::selectOne('SELECT image_path FROM news_images WHERE id = ? AND news_id = ?', [$deleteImgId, $newsId]);
                    if ($imgRow) {
                        // Delete file
                        if (file_exists($dir.$imgRow['image_path'])) {
                            unlink($dir.$imgRow['image_path']);
                        }
                        // Delete from database
                        DB::delete('DELETE FROM news_images WHERE id = ?', [$deleteImgId]);
                    }
                }
            }

            // Handle updating captions of existing images
            $existingCaptions = $request->input('existing_captions');
            if (is_array($existingCaptions)) {
                foreach ($existingCaptions as $imgId => $caption) {
                    $imgId = intval($imgId);
                    $caption = trim((string) $caption);
                    DB::update('UPDATE news_images SET caption = ? WHERE id = ? AND news_id = ?', [$caption ? $caption : null, $imgId, $newsId]);
                }
            }

            // Handle new additional images upload
            $files = H::fileList($request, 'additional_images');
            if ($files !== null) {
                $totalFiles = count($files);
                $newCaptions = $request->input('new_captions');

                // Get current max sort order
                $maxSortRow = DB::selectOne('SELECT MAX(sort_order) as max_sort FROM news_images WHERE news_id = ?', [$newsId]);
                $sortOrder = ($maxSortRow['max_sort'] !== null) ? $maxSortRow['max_sort'] + 1 : 0;

                for ($i = 0; $i < $totalFiles; $i++) {
                    $f = $files[$i] ?? null;
                    if ($f && $f->isValid() && $f->getSize() > 0) {
                        $imageFileType = strtolower(pathinfo($f->getClientOriginalName(), PATHINFO_EXTENSION));

                        // Validate file type
                        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (! in_array($imageFileType, $allowedTypes)) {
                            continue;
                        }

                        $imageName = time().'_'.uniqid().'_'.$i.'.'.$imageFileType;

                        $check = @getimagesize($f->getRealPath());
                        if ($check !== false) {
                            if (H::move($f, $dir.$imageName)) {
                                $caption = isset($newCaptions[$i]) ? trim((string) $newCaptions[$i]) : '';
                                DB::insert(
                                    'INSERT INTO news_images (news_id, image_path, sort_order, caption) VALUES (?, ?, ?, ?)',
                                    [$newsId, $imageName, (string) $sortOrder, $caption ? $caption : null]
                                );
                                $sortOrder++;
                            }
                        }
                    }
                }
            }

            kc_redirect('news-events.php?success=updated');
        }

        // Re-fetch images after potential updates
        $existingImages = DB::select('SELECT * FROM news_images WHERE news_id = ? ORDER BY sort_order ASC', [$newsId]);

        return view('admin.news-edit', compact('newsData', 'existingImages', 'error', 'success'));
    }

    /** admin/news-events.php */
    public function newsEvents(Request $request)
    {
        // Check login
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        $success = null;
        $error = null;

        // Handle Delete News
        if ($request->query('delete_news') !== null) {
            DB::delete('DELETE FROM news_posts WHERE id = ?', [(string) $request->query('delete_news')]);
            $success = 'News post deleted successfully!';
        }

        // Handle Delete Event
        if ($request->query('delete_event') !== null) {
            DB::delete('DELETE FROM event_items WHERE id = ?', [(string) $request->query('delete_event')]);
            $success = 'Event deleted successfully!';
        }

        // Handle Status Toggle News
        if ($request->query('toggle_news') !== null) {
            DB::update('UPDATE news_posts SET status = NOT status WHERE id = ?', [(string) $request->query('toggle_news')]);
            $success = 'News status updated!';
        }

        // Handle Status Toggle Event
        if ($request->query('toggle_event') !== null) {
            DB::update('UPDATE event_items SET status = NOT status WHERE id = ?', [(string) $request->query('toggle_event')]);
            $success = 'Event status updated!';
        }

        // Get all news posts
        $newsRows = DB::select('SELECT * FROM news_posts ORDER BY created_at DESC');

        // Get all events
        $eventRows = DB::select('SELECT * FROM event_items ORDER BY event_date ASC');

        return view('admin.news-events', compact('newsRows', 'eventRows', 'success', 'error'));
    }

    /** admin/event-add.php */
    public function eventAdd(Request $request)
    {
        // Check login
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        $error = null;

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = (string) $request->input('title');
            $event_date = (string) $request->input('event_date');
            $event_time = ! empty($request->input('event_time')) ? (string) $request->input('event_time') : null;
            $location = ! empty($request->input('location')) ? (string) $request->input('location') : null;
            $category = (string) $request->input('category');
            $rsvp_required = $request->has('rsvp_required') ? 1 : 0;
            $rsvp_url = ! empty($request->input('rsvp_url')) ? (string) $request->input('rsvp_url') : null;
            $description = ! empty($request->input('description')) ? (string) $request->input('description') : null;
            $status = $request->has('status') ? 1 : 0;

            DB::insert(
                'INSERT INTO event_items (title, event_date, event_time, location, category, rsvp_required, rsvp_url, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $event_date, $event_time, $location, $category, (string) $rsvp_required, $rsvp_url, $description, (string) $status]
            );
            kc_redirect('news-events.php?success=added');
        }

        return view('admin.event-add', compact('error'));
    }

    /** admin/event-edit.php */
    public function eventEdit(Request $request)
    {
        // Check login
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        // Get event ID
        if ($request->query('id') === null) {
            kc_redirect('news-events.php');
        }

        $id = (string) $request->query('id');

        // Fetch event data
        $event = DB::selectOne('SELECT * FROM event_items WHERE id = ?', [$id]);

        if (! $event) {
            kc_redirect('news-events.php');
        }

        $error = null;

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = (string) $request->input('title');
            $event_date = (string) $request->input('event_date');
            $event_time = ! empty($request->input('event_time')) ? (string) $request->input('event_time') : null;
            $location = ! empty($request->input('location')) ? (string) $request->input('location') : null;
            $category = (string) $request->input('category');
            $rsvp_required = $request->has('rsvp_required') ? 1 : 0;
            $rsvp_url = ! empty($request->input('rsvp_url')) ? (string) $request->input('rsvp_url') : null;
            $description = ! empty($request->input('description')) ? (string) $request->input('description') : null;
            $status = $request->has('status') ? 1 : 0;

            DB::update(
                'UPDATE event_items SET title = ?, event_date = ?, event_time = ?, location = ?, category = ?, rsvp_required = ?, rsvp_url = ?, description = ?, status = ? WHERE id = ?',
                [$title, $event_date, $event_time, $location, $category, (string) $rsvp_required, $rsvp_url, $description, (string) $status, $id]
            );
            kc_redirect('news-events.php?success=updated');
        }

        return view('admin.event-edit', compact('event', 'error'));
    }

    /** admin/manage-birthdays.php */
    public function manageBirthdays(Request $request)
    {
        // Check if user is logged in
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        $get = $request->query();
        $error = null;

        // ============ BIRTHDAY HANDLERS ============
        // Handle delete birthday
        if (isset($get['delete_birthday'])) {
            $id = (int) $get['delete_birthday'];

            // Get photo to delete
            $item = DB::selectOne('SELECT photo FROM birthdays WHERE id = ?', [$id]);
            if ($item && $item['photo']) {
                $filepath = kc_public_file('uploads/'.$item['photo'], 'admin');
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }

            DB::delete('DELETE FROM birthdays WHERE id = ?', [$id]);
            kc_redirect('manage-birthdays.php?tab=birthdays&success=deleted');
        }

        // Handle status toggle birthday
        if (isset($get['toggle_birthday'])) {
            $id = (int) $get['toggle_birthday'];
            $current = DB::selectOne('SELECT status FROM birthdays WHERE id = ?', [$id]);
            $newStatus = ($current['status'] ?? null) ? 0 : 1;
            DB::update('UPDATE birthdays SET status = ? WHERE id = ?', [$newStatus, $id]);
            kc_redirect('manage-birthdays.php?tab=birthdays&success=toggled');
        }

        // ============ SOCIAL LINKS HANDLERS ============
        // Handle delete social link
        if (isset($get['delete_social'])) {
            $id = (int) $get['delete_social'];
            DB::delete('DELETE FROM social_links WHERE id = ?', [$id]);
            kc_redirect('manage-birthdays.php?tab=social&success=deleted');
        }

        // Handle status toggle social link
        if (isset($get['toggle_social'])) {
            $id = (int) $get['toggle_social'];
            $current = DB::selectOne('SELECT status FROM social_links WHERE id = ?', [$id]);
            $newStatus = ($current['status'] ?? null) ? 0 : 1;
            DB::update('UPDATE social_links SET status = ? WHERE id = ?', [$newStatus, $id]);
            kc_redirect('manage-birthdays.php?tab=social&success=toggled');
        }

        // Handle social link form submission
        if ($request->isMethod('post') && $request->has('social_submit')) {
            $platform = sanitize($request->input('platform'));
            $icon = sanitize($request->input('icon'));
            $url = sanitize($request->input('url'));
            $color = sanitize($request->input('color'));
            $bg_color = sanitize($request->input('bg_color'));
            $username = sanitize($request->input('username'));
            $followers_count = sanitize($request->input('followers_count'));
            $description = sanitize($request->input('description'));
            $sort_order = (int) $request->input('sort_order');
            $status = $request->has('status') ? 1 : 0;

            if ($request->has('social_id') && ! empty($request->input('social_id'))) {
                // Update
                $id = (int) $request->input('social_id');
                DB::update(
                    'UPDATE social_links SET platform = ?, icon = ?, url = ?, color = ?, bg_color = ?, username = ?, followers_count = ?, description = ?, sort_order = ?, status = ? WHERE id = ?',
                    [$platform, $icon, $url, $color, $bg_color, $username, $followers_count, $description, (string) $sort_order, (string) $status, $id]
                );
            } else {
                // Insert
                DB::insert(
                    'INSERT INTO social_links (platform, icon, url, color, bg_color, username, followers_count, description, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$platform, $icon, $url, $color, $bg_color, $username, $followers_count, $description, (string) $sort_order, (string) $status]
                );
            }
            kc_redirect('manage-birthdays.php?tab=social&success=saved');
        }

        // Handle birthday form submission
        if ($request->isMethod('post') && $request->has('birthday_submit')) {
            $full_name = sanitize($request->input('full_name'));
            $department = sanitize($request->input('department'));
            $birth_date = sanitize($request->input('birth_date'));
            $message = sanitize($request->input('message'));
            $status = $request->has('status') ? 1 : 0;

            // Handle photo upload
            $photo = '';
            $file = $request->file('photo');
            if ($file && $file->isValid()) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $file->getClientOriginalName();
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $newFilename = 'bday_'.time().'_'.preg_replace('/[^a-zA-Z0-9.]/', '_', $filename);
                    $uploadPath = kc_public_file('uploads/'.$newFilename, 'admin');

                    // Create uploads directory if it doesn't exist
                    if (! file_exists(kc_public_file('uploads', 'admin'))) {
                        mkdir(kc_public_file('uploads', 'admin'), 0777, true);
                    }

                    if (H::move($file, $uploadPath)) {
                        $photo = $newFilename;

                        // Delete old photo if editing
                        if ($request->has('birthday_id') && ! empty($request->input('birthday_id'))) {
                            $old = DB::selectOne('SELECT photo FROM birthdays WHERE id = ?', [(int) $request->input('birthday_id')]);
                            if ($old && $old['photo'] && file_exists(kc_public_file('uploads/'.$old['photo'], 'admin'))) {
                                unlink(kc_public_file('uploads/'.$old['photo'], 'admin'));
                            }
                        }
                    } else {
                        $error = 'Failed to upload photo';
                    }
                } else {
                    $error = 'Invalid file type. Allowed: '.implode(', ', $allowed);
                }
            } elseif ($request->has('existing_photo')) {
                $photo = $request->input('existing_photo');
            }

            if (! isset($error)) {
                if ($request->has('birthday_id') && ! empty($request->input('birthday_id'))) {
                    // Update
                    $id = (int) $request->input('birthday_id');
                    $sql = 'UPDATE birthdays SET full_name = ?, department = ?, birth_date = ?, message = ?, status = ?';
                    $bindings = [$full_name, $department, $birth_date, $message, $status];

                    if ($photo) {
                        $sql .= ', photo = ?';
                        $bindings[] = $photo;
                    }

                    $sql .= ' WHERE id = ?';
                    $bindings[] = $id;
                    DB::update($sql, $bindings);
                } else {
                    // Insert
                    DB::insert(
                        'INSERT INTO birthdays (full_name, photo, department, birth_date, message, status) VALUES (?, ?, ?, ?, ?, ?)',
                        [$full_name, $photo ? $photo : null, $department, $birth_date, $message, $status]
                    );
                }
                kc_redirect('manage-birthdays.php?tab=birthdays&success=saved');
            }
        }

        // Get current tab
        $activeTab = isset($get['tab']) ? $get['tab'] : 'birthdays';

        // Get all birthdays ordered by month and day
        $birthdays = DB::select('SELECT * FROM birthdays ORDER BY MONTH(birth_date), DAY(birth_date)');

        // Get all social links
        $socialLinks = DB::select('SELECT * FROM social_links ORDER BY sort_order ASC');

        // Get edit data if editing birthday
        $editBirthday = null;
        if (isset($get['edit_birthday'])) {
            $id = (int) $get['edit_birthday'];
            $editBirthday = DB::selectOne('SELECT * FROM birthdays WHERE id = ?', [$id]);
        }

        // Get edit data if editing social
        $editSocial = null;
        if (isset($get['edit_social'])) {
            $id = (int) $get['edit_social'];
            $editSocial = DB::selectOne('SELECT * FROM social_links WHERE id = ?', [$id]);
        }

        // Common social media icons
        $socialIcons = [
            'fa-linkedin-in' => 'LinkedIn',
            'fa-twitter' => 'Twitter',
            'fa-facebook-f' => 'Facebook',
            'fa-instagram' => 'Instagram',
            'fa-youtube' => 'YouTube',
            'fa-whatsapp' => 'WhatsApp',
            'fa-telegram' => 'Telegram',
            'fa-tiktok' => 'TikTok',
            'fa-snapchat' => 'Snapchat',
            'fa-pinterest' => 'Pinterest',
            'fa-reddit' => 'Reddit',
            'fa-discord' => 'Discord',
            'fa-github' => 'GitHub',
            'fa-medium' => 'Medium',
            'fa-twitch' => 'Twitch',
            'fa-vimeo' => 'Vimeo',
        ];

        // Success messages
        $successMessages = [
            'saved' => 'Record saved successfully!',
            'deleted' => 'Record deleted successfully!',
            'toggled' => 'Status updated successfully!',
        ];

        return view('admin.manage-birthdays', compact(
            'get', 'error', 'activeTab', 'birthdays', 'socialLinks', 'editBirthday', 'editSocial', 'socialIcons', 'successMessages'
        ));
    }

    /** admin/normalize-birthdays.php */
    public function normalizeBirthdays(Request $request)
    {
        // Check if user is logged in
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        // Get current user info with better error handling
        $username = session('username');
        $user = null;
        $isAdmin = false;

        if ($username) {
            $user = DB::selectOne('SELECT role FROM admin_users WHERE username = ?', [(string) $username]);
            if ($user) {
                $isAdmin = ($user['role'] === 'admin');
            }
        }

        // Only allow admin to run this
        if (! $isAdmin) {
            kc_exit(view('admin.normalize-birthdays-denied')->render());
        }

        $message = '';
        $error = '';

        // Handle the normalization
        if ($request->has('normalize') || $request->query('confirm') !== null) {
            // Get all birthdays
            $rows = DB::select('SELECT id, full_name, department FROM birthdays');

            if ($rows !== null) {
                $updated = 0;
                $errors = 0;
                $total = count($rows);

                foreach ($rows as $row) {
                    // Normalize full_name - handle NULL values
                    $original_name = $row['full_name'] ?? '';
                    $normalized_name = ! empty($original_name) ? ucwords(strtolower(trim($original_name))) : '';

                    // Normalize department - handle NULL values
                    $original_dept = $row['department'] ?? '';
                    $normalized_dept = ! empty($original_dept) ? ucwords(strtolower(trim($original_dept))) : '';

                    // Update the record
                    DB::update('UPDATE birthdays SET full_name = ?, department = ? WHERE id = ?', [$normalized_name, $normalized_dept, (int) $row['id']]);
                    $updated++;
                }

                $message = "Processed $total records. Updated: $updated, Errors: $errors.";

                if ($errors > 0) {
                    $message .= ' Check error log for details.';
                }
            }
        }

        // Get current data to preview (limited to 50 for performance)
        $preview_limit = 50;
        $birthdays = DB::select("SELECT id, full_name, department, birth_date FROM birthdays ORDER BY full_name LIMIT $preview_limit");

        // Get total count
        $total_count = DB::selectOne('SELECT COUNT(*) as total FROM birthdays')['total'];

        return view('admin.normalize-birthdays', compact('message', 'error', 'birthdays', 'total_count', 'preview_limit', 'username'));
    }

    /** admin/manage-carousel.php */
    public function manageCarousel(Request $request)
    {
        // Check if user is logged in
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        $get = $request->query();
        $error = null;

        // Handle delete
        if (isset($get['delete'])) {
            $id = (int) $get['delete'];

            // Get file to delete
            $item = DB::selectOne('SELECT media_url, media_type FROM homepage_carousel WHERE id = ?', [$id]);
            if ($item) {
                if ($item['media_type'] == 'image' && $item['media_url']) {
                    $filepath = kc_upload_path().$item['media_url'];
                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
            }

            DB::delete('DELETE FROM homepage_carousel WHERE id = ?', [$id]);
            kc_redirect('manage-carousel.php');
        }

        // Handle status toggle
        if (isset($get['toggle'])) {
            $id = (int) $get['toggle'];
            $current = DB::selectOne('SELECT status FROM homepage_carousel WHERE id = ?', [$id]);
            if ($current) {
                $newStatus = $current['status'] ? 0 : 1;
                DB::update('UPDATE homepage_carousel SET status = ? WHERE id = ?', [$newStatus, $id]);
            }
            kc_redirect('manage-carousel.php');
        }

        // Handle add/edit
        if ($request->isMethod('post')) {
            $title = (string) $request->input('title');
            $media_type = (string) $request->input('media_type');
            $description = (string) $request->input('description'); // Don't escape - allow HTML
            $sort_order = (int) $request->input('sort_order');
            $status = $request->has('status') ? 1 : 0;

            // Handle media URL or file upload
            $file = $request->file('image');
            if ($media_type == 'image' && $file && $file->isValid()) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $file->getClientOriginalName();
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $newFilename = time().'_'.preg_replace('/[^a-zA-Z0-9.]/', '_', $filename);
                    $uploadPath = kc_upload_path().$newFilename;

                    if (H::move($file, $uploadPath)) {
                        $media_url = $newFilename;

                        // Delete old image if editing
                        if ($request->has('id') && ! empty($request->input('id'))) {
                            $old = DB::selectOne('SELECT media_url FROM homepage_carousel WHERE id = ?', [(int) $request->input('id')]);
                            if ($old) {
                                if ($old['media_url'] && file_exists(kc_upload_path().$old['media_url'])) {
                                    unlink(kc_upload_path().$old['media_url']);
                                }
                            }
                        }
                    } else {
                        $error = 'Failed to upload image';
                    }
                } else {
                    $error = 'Invalid file type. Allowed: '.implode(', ', $allowed);
                }
            } else {
                $media_url = (string) $request->input('media_url');

                // Extract YouTube/Vimeo ID
                if ($media_type == 'youtube') {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $media_url, $matches);
                    if (isset($matches[1])) {
                        $media_url = $matches[1];
                    }
                } elseif ($media_type == 'vimeo') {
                    preg_match('/(?:vimeo\.com\/(?:video\/)?)(\d+)/', $media_url, $matches);
                    if (isset($matches[1])) {
                        $media_url = $matches[1];
                    }
                }
            }

            if (! isset($error) && isset($media_url)) {
                if ($request->has('id') && ! empty($request->input('id'))) {
                    // Update
                    $id = (int) $request->input('id');
                    DB::update(
                        'UPDATE homepage_carousel SET title = ?, media_type = ?, media_url = ?, description = ?, sort_order = ?, status = ? WHERE id = ?',
                        [$title, $media_type, $media_url, $description, $sort_order, $status, $id]
                    );
                } else {
                    // Insert
                    DB::insert(
                        'INSERT INTO homepage_carousel (title, media_type, media_url, description, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)',
                        [$title, $media_type, $media_url, $description, $sort_order, $status]
                    );
                }
                kc_redirect('manage-carousel.php');
            } elseif (! isset($media_url)) {
                $error = 'Please provide a media URL or upload an image';
            }
        }

        // Get all carousel items
        $carousel = DB::select('SELECT * FROM homepage_carousel ORDER BY sort_order ASC');

        // Get edit data if editing
        $editData = null;
        if (isset($get['edit'])) {
            $id = (int) $get['edit'];
            $editData = DB::selectOne('SELECT * FROM homepage_carousel WHERE id = ?', [$id]);
        }

        return view('admin.manage-carousel', compact('get', 'error', 'carousel', 'editData'));
    }

    /** admin/manage-spotlights.php */
    public function manageSpotlights(Request $request)
    {
        // Check if user is logged in
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('login.php');
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = kc_public_file('../uploads/', 'admin');
        if (! file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Handle delete
        if ($request->query('delete') !== null) {
            $id = (int) $request->query('delete');

            // Get photo to delete
            $item = DB::selectOne('SELECT employee_photo FROM employee_spotlights WHERE id = ?', [$id]);
            if ($item) {
                if ($item['employee_photo'] && file_exists($uploadDir.$item['employee_photo'])) {
                    unlink($uploadDir.$item['employee_photo']);
                }
            }

            DB::delete('DELETE FROM employee_spotlights WHERE id = ?', [$id]);
            session(['message' => 'Spotlight deleted successfully!', 'message_type' => 'success']);
            kc_redirect('manage-spotlights.php');
        }

        // Handle status toggle
        if ($request->query('toggle') !== null) {
            $id = (int) $request->query('toggle');
            $current = DB::selectOne('SELECT status FROM employee_spotlights WHERE id = ?', [$id]);
            if ($current) {
                $newStatus = $current['status'] ? 0 : 1;
                DB::update('UPDATE employee_spotlights SET status = ? WHERE id = ?', [$newStatus, $id]);
                session(['message' => 'Status updated successfully!', 'message_type' => 'success']);
            }
            kc_redirect('manage-spotlights.php');
        }

        // Handle add/edit
        $error = null;
        $success = null;

        if ($request->isMethod('post')) {
            $employee_name = trim((string) $request->input('employee_name'));
            $spotlight_type = $request->input('spotlight_type');
            $achievement_title = trim((string) $request->input('achievement_title'));
            $achievement_description = trim((string) $request->input('achievement_description'));
            $rating = ! empty($request->input('rating')) ? (float) $request->input('rating') : null;
            $department = trim((string) $request->input('department'));
            $featured_quote = trim((string) $request->input('featured_quote'));
            $badge_text = trim((string) $request->input('badge_text'));
            $badge_color = $request->input('badge_color');
            $sort_order = (int) $request->input('sort_order');
            $status = $request->has('status') ? 1 : 0;
            $id = $request->has('id') && ! empty($request->input('id')) ? (int) $request->input('id') : 0;

            // Validate required fields
            if (empty($employee_name) || empty($achievement_title)) {
                $error = 'Name and Achievement Title are required.';
            } else {
                // Handle photo upload
                $photo = null;
                $file = $request->file('employee_photo');

                if ($file && $file->isValid() && $file->getSize() > 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $filename = $file->getClientOriginalName();
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $fileSize = $file->getSize();
                    $maxSize = 5 * 1024 * 1024; // 5MB

                    if ($fileSize > $maxSize) {
                        $error = 'File size too large. Maximum 5MB allowed.';
                    } elseif (! in_array($ext, $allowed)) {
                        $error = 'Invalid file type. Allowed: '.implode(', ', $allowed);
                    } else {
                        $newFilename = 'spotlight_'.time().'_'.rand(1000, 9999).'.'.$ext;
                        $uploadPath = $uploadDir.$newFilename;

                        if (H::move($file, $uploadPath)) {
                            // Delete old photo if editing
                            if ($id > 0) {
                                $old = DB::selectOne('SELECT employee_photo FROM employee_spotlights WHERE id = ?', [$id]);
                                if ($old) {
                                    if (! empty($old['employee_photo']) && file_exists($uploadDir.$old['employee_photo'])) {
                                        unlink($uploadDir.$old['employee_photo']);
                                    }
                                }
                            }
                            $photo = $newFilename;
                        } else {
                            $error = 'Failed to upload photo. Please check folder permissions.';
                        }
                    }
                } elseif ($request->has('existing_photo') && ! empty($request->input('existing_photo'))) {
                    $photo = $request->input('existing_photo');
                }

                if (! $error) {
                    $rating_safe = $rating !== null ? (float) $rating : null;

                    if ($id > 0) {
                        // Update existing
                        $sql = 'UPDATE employee_spotlights SET employee_name = ?, spotlight_type = ?, achievement_title = ?, achievement_description = ?, rating = ?, department = ?, featured_quote = ?, badge_text = ?, badge_color = ?, sort_order = ?, status = ?';
                        $bindings = [$employee_name, (string) $spotlight_type, $achievement_title, $achievement_description, $rating_safe, $department, $featured_quote, $badge_text, (string) $badge_color, $sort_order, $status];

                        if ($photo) {
                            $sql .= ', employee_photo = ?';
                            $bindings[] = $photo;
                        }

                        $sql .= ' WHERE id = ?';
                        $bindings[] = $id;

                        DB::update($sql, $bindings);
                        session(['message' => 'Spotlight updated successfully!', 'message_type' => 'success']);
                        kc_redirect('manage-spotlights.php');
                    } else {
                        // Insert new
                        DB::insert(
                            'INSERT INTO employee_spotlights (employee_name, employee_photo, spotlight_type, achievement_title, achievement_description, rating, department, featured_quote, badge_text, badge_color, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            [$employee_name, $photo ? $photo : null, (string) $spotlight_type, $achievement_title, $achievement_description, $rating_safe, $department, $featured_quote, $badge_text, (string) $badge_color, $sort_order, $status]
                        );
                        session(['message' => 'Spotlight added successfully!', 'message_type' => 'success']);
                        kc_redirect('manage-spotlights.php');
                    }
                }
            }
        }

        // Get all spotlights
        $spotlights = DB::select('SELECT * FROM employee_spotlights ORDER BY sort_order ASC, created_at DESC');

        // Get edit data if editing
        $editData = null;
        if ($request->query('edit') !== null && is_numeric($request->query('edit'))) {
            $id = (int) $request->query('edit');
            $editData = DB::selectOne('SELECT * FROM employee_spotlights WHERE id = ?', [$id]);
        }

        // Badge color options
        $badgeColors = [
            'primary' => 'Blue',
            'success' => 'Green',
            'warning' => 'Orange',
            'danger' => 'Red',
            'info' => 'Light Blue',
            'dark' => 'Dark',
            'secondary' => 'Gray',
        ];

        [$flashMessage, $flashType] = H::pullFlash();

        return view('admin.manage-spotlights', compact('error', 'success', 'spotlights', 'editData', 'badgeColors', 'flashMessage', 'flashType'));
    }

    /** admin/manage-gallery.php */
    public function manageGallery(Request $request)
    {
        // Check if admin is logged in
        if (! session()->has('admin_id')) {
            kc_redirect('login.php');
        }

        $get = $request->query();

        // Handle delete
        if (isset($get['delete']) && is_numeric($get['delete'])) {
            $id = (int) $get['delete'];

            // Get thumbnail before deleting
            $item = DB::selectOne('SELECT thumbnail FROM gallery_items WHERE id = ?', [$id]);

            if ($item && $item['thumbnail']) {
                $thumb_path = kc_public_file('../'.$item['thumbnail'], 'admin');
                if (file_exists($thumb_path)) {
                    unlink($thumb_path);
                }
            }

            DB::delete('DELETE FROM gallery_items WHERE id = ?', [$id]);
            session(['message' => 'Gallery item deleted successfully!', 'message_type' => 'success']);
            kc_redirect('manage-gallery.php');
        }

        // Handle toggle status
        if (isset($get['toggle']) && is_numeric($get['toggle'])) {
            $id = (int) $get['toggle'];
            DB::update('UPDATE gallery_items SET status = NOT status WHERE id = ?', [$id]);
            session(['message' => 'Status updated successfully!', 'message_type' => 'success']);
            kc_redirect('manage-gallery.php');
        }

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = sanitize($request->input('title'));
            $description = sanitize($request->input('description'));
            $external_url = sanitize($request->input('external_url'));
            $source_type = sanitize($request->input('source_type'));
            $category = sanitize($request->input('category'));
            $edit_id = $request->has('edit_id') ? (int) $request->input('edit_id') : 0;

            if (empty($title)) {
                session(['message' => 'Title is required!', 'message_type' => 'danger']);
                kc_redirect('manage-gallery.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            if (empty($external_url)) {
                session(['message' => 'External URL is required!', 'message_type' => 'danger']);
                kc_redirect('manage-gallery.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            // Validate URL
            if (! filter_var($external_url, FILTER_VALIDATE_URL)) {
                session(['message' => 'Please enter a valid URL (include https://)', 'message_type' => 'danger']);
                kc_redirect('manage-gallery.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            $file = $request->file('thumbnail');
            if ($edit_id > 0) {
                // Update existing item
                if ($file && $file->isValid()) {
                    // Upload new thumbnail
                    $upload = $this->uploadThumbnail($file);
                    if ($upload['success']) {
                        // Delete old thumbnail
                        $old_item = DB::selectOne('SELECT thumbnail FROM gallery_items WHERE id = ?', [$edit_id]);
                        if ($old_item && $old_item['thumbnail']) {
                            $old_path = kc_public_file('../'.$old_item['thumbnail'], 'admin');
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                        }

                        $thumbnail = $upload['filepath'];
                        $sql = 'UPDATE gallery_items SET title = ?, description = ?, thumbnail = ?, external_url = ?, source_type = ?, category = ? WHERE id = ?';
                        $bindings = [$title, $description, $thumbnail, $external_url, $source_type, $category, $edit_id];
                    } else {
                        session(['message' => 'Error uploading thumbnail: '.$upload['error'], 'message_type' => 'danger']);
                        kc_redirect('manage-gallery.php?edit='.$edit_id);
                    }
                } else {
                    // Update without changing thumbnail
                    $sql = 'UPDATE gallery_items SET title = ?, description = ?, external_url = ?, source_type = ?, category = ? WHERE id = ?';
                    $bindings = [$title, $description, $external_url, $source_type, $category, $edit_id];
                }

                DB::update($sql, $bindings);
                session(['message' => 'Gallery item updated successfully!', 'message_type' => 'success']);
            } else {
                // Insert new item
                if ($file && $file->isValid()) {
                    $upload = $this->uploadThumbnail($file);
                    if ($upload['success']) {
                        $thumbnail = $upload['filepath'];

                        // Get max sort order
                        $max_order = DB::selectOne('SELECT MAX(sort_order) as max FROM gallery_items')['max'];
                        $sort_order = ($max_order !== null) ? $max_order + 1 : 0;

                        DB::insert(
                            'INSERT INTO gallery_items (title, description, thumbnail, external_url, source_type, category, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)',
                            [$title, $description, $thumbnail, $external_url, $source_type, $category, $sort_order]
                        );
                        session(['message' => 'Gallery item added successfully!', 'message_type' => 'success']);
                    } else {
                        session(['message' => 'Error uploading thumbnail: '.$upload['error'], 'message_type' => 'danger']);
                        kc_redirect('manage-gallery.php');
                    }
                } else {
                    session(['message' => 'Please select a thumbnail image!', 'message_type' => 'danger']);
                    kc_redirect('manage-gallery.php');
                }
            }

            kc_redirect('manage-gallery.php');
        }

        // Get all gallery items
        $gallery_items = DB::select('SELECT * FROM gallery_items ORDER BY sort_order, created_at DESC');

        // Get categories
        $categories = DB::select('SELECT * FROM gallery_categories WHERE status = 1 ORDER BY sort_order');

        // Get item for editing
        $edit_item = null;
        if (isset($get['edit']) && is_numeric($get['edit'])) {
            $edit_id = (int) $get['edit'];
            $edit_item = DB::selectOne('SELECT * FROM gallery_items WHERE id = ?', [$edit_id]);
        }

        [$flashMessage, $flashType] = H::pullFlash();

        // Stats
        $total_items = DB::selectOne('SELECT COUNT(*) as total FROM gallery_items')['total'];
        $active_items = DB::selectOne('SELECT COUNT(*) as total FROM gallery_items WHERE status = 1')['total'];
        $total_categories = DB::selectOne('SELECT COUNT(DISTINCT category) as total FROM gallery_items')['total'];

        return view('admin.manage-gallery', compact(
            'get', 'gallery_items', 'categories', 'edit_item', 'flashMessage', 'flashType', 'total_items', 'active_items', 'total_categories'
        ));
    }

    /** uploadThumbnail() from admin/manage-gallery.php */
    private function uploadThumbnail($file): array
    {
        $target_dir = kc_public_file('../uploads/gallery/', 'admin');
        if (! file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (! in_array($file_extension, $allowed)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
        }

        $new_filename = 'gallery_'.time().'_'.uniqid().'.'.$file_extension;
        $target_file = $target_dir.$new_filename;

        if (H::move($file, $target_file)) {
            return [
                'success' => true,
                'filename' => $new_filename,
                'filepath' => 'uploads/gallery/'.$new_filename,
            ];
        }

        return ['success' => false, 'error' => 'Failed to upload file'];
    }

    /** admin/manage-brand-assets.php */
    public function manageBrandAssets(Request $request)
    {
        // Check if admin is logged in
        if (! session()->has('admin_id')) {
            kc_redirect('login.php');
        }

        $get = $request->query();

        // Company definitions (same as frontend for consistency)
        $companies = H::BRAND_COMPANIES;

        // Handle delete
        if (isset($get['delete']) && is_numeric($get['delete'])) {
            $id = (int) $get['delete'];

            $asset = DB::selectOne('SELECT file_path, thumbnail_path FROM brand_assets WHERE id = ?', [$id]);

            if ($asset) {
                if (file_exists(kc_public_file('../'.$asset['file_path'], 'admin'))) {
                    unlink(kc_public_file('../'.$asset['file_path'], 'admin'));
                }
                if ($asset['thumbnail_path'] && file_exists(kc_public_file('../'.$asset['thumbnail_path'], 'admin'))) {
                    unlink(kc_public_file('../'.$asset['thumbnail_path'], 'admin'));
                }

                DB::delete('DELETE FROM brand_assets WHERE id = ?', [$id]);
                session(['message' => 'Asset deleted successfully!', 'message_type' => 'success']);
            }
            kc_redirect('manage-brand-assets.php');
        }

        // Handle toggle status
        if (isset($get['toggle']) && is_numeric($get['toggle'])) {
            $id = (int) $get['toggle'];
            DB::update('UPDATE brand_assets SET status = NOT status WHERE id = ?', [$id]);
            session(['message' => 'Status updated successfully!', 'message_type' => 'success']);
            kc_redirect('manage-brand-assets.php');
        }

        // Handle form submission
        if ($request->isMethod('post')) {
            $title = sanitize($request->input('title'));
            $description = sanitize($request->input('description'));
            $category = sanitize($request->input('category'));
            $company = sanitize($request->input('company') ?? 'general');
            $edit_id = $request->has('edit_id') ? (int) $request->input('edit_id') : 0;

            if (empty($title)) {
                session(['message' => 'Title is required!', 'message_type' => 'danger']);
                kc_redirect('manage-brand-assets.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            // Auto-detect company if set to 'auto'
            if ($company === 'auto') {
                $company = H::detectCompany($title, $description);
            }

            if ($edit_id > 0) {
                // Update existing asset
                DB::update('UPDATE brand_assets SET title = ?, description = ?, category = ? WHERE id = ?', [$title, $description, $category, $edit_id]);
                session(['message' => 'Asset updated successfully!', 'message_type' => 'success']);
            } else {
                // Handle file upload for new asset
                $file = $request->file('asset_file');
                if ($file && $file->isValid()) {
                    $upload = $this->uploadBrandFile($file);

                    if ($upload['success']) {
                        $file_path = $upload['filepath'];
                        $file_size = $upload['filesize'];
                        $file_type = $upload['filetype'];

                        DB::insert(
                            'INSERT INTO brand_assets (title, description, category, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?, ?)',
                            [$title, $description, $category, $file_path, $file_size, $file_type]
                        );
                        session(['message' => 'Asset added successfully! It will appear under <strong>'.htmlspecialchars($companies[$company] ?? '').'</strong>.', 'message_type' => 'success']);
                    } else {
                        session(['message' => 'Error uploading file: '.$upload['error'], 'message_type' => 'danger']);
                    }
                } else {
                    session(['message' => 'Please select a file to upload!', 'message_type' => 'danger']);
                }
            }

            kc_redirect('manage-brand-assets.php');
        }

        // Get all assets with computed company
        $allAssets = [];
        foreach (DB::select('SELECT * FROM brand_assets ORDER BY created_at DESC') as $a) {
            $a['computed_company'] = H::detectCompany($a['title'], $a['description'] ?? '');
            $allAssets[] = $a;
        }

        // Get categories for filter
        $categories = DB::select('SELECT * FROM brand_categories WHERE status = 1 ORDER BY sort_order');

        // Get stats per company
        $companyStats = [];
        foreach ($companies as $ckey => $cname) {
            $count = 0;
            foreach ($allAssets as $a) {
                if ($a['computed_company'] === $ckey) {
                    $count++;
                }
            }
            $companyStats[$ckey] = $count;
        }

        // Get item for editing
        $edit_item = null;
        if (isset($get['edit']) && is_numeric($get['edit'])) {
            $edit_id = (int) $get['edit'];
            $edit_item = DB::selectOne('SELECT * FROM brand_assets WHERE id = ?', [$edit_id]);
            if ($edit_item) {
                $edit_item['computed_company'] = H::detectCompany($edit_item['title'], $edit_item['description'] ?? '');
            }
        }

        [$flashMessage, $flashType] = H::pullFlash();

        // categories for the form's select (the old view queried them again)
        $cats = DB::select('SELECT * FROM brand_categories WHERE status = 1 ORDER BY sort_order');

        return view('admin.manage-brand-assets', compact(
            'get', 'companies', 'allAssets', 'categories', 'companyStats', 'edit_item', 'flashMessage', 'flashType', 'cats'
        ));
    }

    /** uploadFile() from admin/manage-brand-assets.php */
    private function uploadBrandFile($file): array
    {
        $target_dir = kc_public_file('../uploads/brand_assets/', 'admin');
        if (! file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        $new_filename = time().'_'.preg_replace('/[^a-zA-Z0-9.]/', '', $file->getClientOriginalName());
        $target_file = $target_dir.$new_filename;
        $size = $file->getSize();

        if (H::move($file, $target_file)) {
            return [
                'success' => true,
                'filename' => $new_filename,
                'filepath' => 'uploads/brand_assets/'.$new_filename,
                'filesize' => $size,
                'filetype' => $file_extension,
            ];
        }

        return ['success' => false, 'error' => 'Failed to upload file'];
    }
}
