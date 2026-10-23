<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * K-Academy module: shared helpers.
 *
 * K-Academy keeps its own database (the "kacademy" connection, database kiboko_academy) exactly
 * as it is. People log in with their K-Connect employee account only; the K-Academy `users`
 * table is used as a link table: each employee is matched to their K-Academy user by employee
 * code (or e-mail), and a K-Academy user is created automatically the first time an employee
 * opens K-Academy. All K-Academy rows (enrollments, progress, quiz results, certificates...)
 * keep pointing to K-Academy user ids, so existing data carries over unchanged.
 */
class Academy
{
    /**
     * Folder under public/ for K-Academy files: the old kacademy/assets/ (css, js, uploads/{courses,
     * attachments,videos,profiles}). The browser reaches it as /academy/assets/... (see public/.htaccess).
     */
    public const UPLOAD_DIR = 'academy-files/assets/uploads';

    public const CATEGORY_TYPES = [
        'foundation' => 'K-Foundation',
        'mastery' => 'K-Mastery',
        'resource' => 'K-Resource Hub',
        'compliance' => 'K-Compliance Center',
    ];

    public static function db(): ConnectionInterface
    {
        $conn = getKADB();
        if (! $conn) {
            $reason = '';
            try {
                \Illuminate\Support\Facades\DB::connection('kacademy')->getPdo();
            } catch (\Throwable $e) {
                $reason = $e->getMessage();
                \Illuminate\Support\Facades\Log::error('K-Academy database (KA_DB_* in .env) could not be reached: '.$reason);
            }
            $message = 'K-Academy is not available right now (its database could not be reached).';
            if (config('app.debug') && $reason !== '') {
                $message .= ' Check the KA_DB_* settings in .env and that the database '.config('database.connections.kacademy.database').' exists. Error: '.$reason;
            }
            throw new HttpException(503, $message);
        }

        return $conn;
    }

    /**
     * PDO of the "kacademy" connection, for the K-Academy pages ported from the old site
     * (prepare/execute/fetch exactly as before, rows as associative arrays).
     */
    public static function pdo(): \PDO
    {
        $pdo = self::db()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * Variables every K-Academy page had from config.php, includes/header.php and includes/sidebar.php:
     * $pdo, $user, $user_id, $user_name, $user_role, notification and certificate counts.
     * Learner pages need an employee login, admin pages (academy/admin/...) a K-Connect admin login.
     */
    public static function pageVars(bool $admin, string $page): array
    {
        // Old pages read and wrote files relative to the kacademy/ folder (assets/uploads/...).
        @chdir(public_path('academy-files'));

        $pdo = self::pdo();
        $ka = self::db();
        if ($admin) {
            self::requireAdmin();
            $user_id = self::adminUserId();
            $user = $user_id ? ($ka->selectOne('SELECT * FROM users WHERE id = ?', [$user_id]) ?: null) : null;
            $user_name = session('admin_username') ?: ($user['full_name'] ?? 'Administrator');
            $user_role = 'admin';
            $user_code = $user['employee_code'] ?? '';
        } else {
            $user = self::requireLearner();
            $user_id = (int) $user['id'];
            $user_name = $user['full_name'];
            $user_role = $user['role'];
            $user_code = $user['employee_code'];
        }

        $recent_notifications = [];
        $unread_count = 0;
        if ($user_id) {
            $recent_notifications = $ka->select('SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5', [$user_id]);
            $unread_count = (int) ($ka->selectOne('SELECT COUNT(*) c FROM notifications WHERE user_id = ? AND is_read = 0', [$user_id])['c'] ?? 0);
        }
        $pending_count = $admin ? (int) ($ka->selectOne("SELECT COUNT(*) c FROM users WHERE status = 'pending'")['c'] ?? 0) : 0;
        $pending_certificates = $admin ? (int) ($ka->selectOne("SELECT COUNT(*) c FROM certificates WHERE status = 'pending'")['c'] ?? 0) : 0;
        $cert_count = ! $admin ? (int) ($ka->selectOne('SELECT COUNT(*) c FROM certificates WHERE user_id = ?', [$user_id])['c'] ?? 0) : 0;

        return [
            'pdo' => $pdo,
            'user' => $user,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_role' => $user_role,
            'user_code' => $user_code,
            'is_admin_area' => $admin,
            'current_page' => $page.'.php',
            'recent_notifications' => $recent_notifications,
            'unread_count' => $unread_count,
            'unread_notifications' => $unread_count,
            'pending_count' => $pending_count,
            'pending_certificates' => $pending_certificates,
            'cert_count' => $cert_count,
        ];
    }

    /** Same variables as pageVars() for pages anyone may open (landing page, certificate check). */
    public static function publicVars(string $page): array
    {
        @chdir(public_path('academy-files'));
        $user = self::learner();
        if ($user && $user['status'] !== 'active') {
            $user = null;
        }
        $vars = [
            'pdo' => self::pdo(), 'user' => $user, 'user_id' => $user['id'] ?? null, 'user_name' => $user['full_name'] ?? null,
            'user_role' => $user['role'] ?? null, 'user_code' => $user['employee_code'] ?? null, 'is_admin_area' => false,
            'current_page' => $page.'.php', 'recent_notifications' => [], 'unread_count' => 0, 'unread_notifications' => 0,
            'pending_count' => 0, 'pending_certificates' => 0, 'cert_count' => 0,
        ];
        if ($user) {
            $vars = array_merge($vars, array_intersect_key(self::pageVars(false, $page), array_flip(['recent_notifications', 'unread_count', 'unread_notifications', 'cert_count'])));
        }

        return $vars;
    }

    /**
     * The K-Academy user row for the logged-in employee, created or re-activated when needed.
     * Returns null when no employee is logged in.
     */
    public static function learner(): ?array
    {
        static $cache = [];
        $employeeId = session('employee_id');
        if (! $employeeId) {
            // A K-Connect admin who is not also signed in as an employee opens the learner pages
            // as their K-Academy admin account, so no second login is needed.
            if (isAdminLoggedIn() && ($adminId = self::adminUserId())) {
                return $cache['admin'] ??= (self::db()->selectOne('SELECT * FROM users WHERE id = ?', [$adminId]) ?: null);
            }

            return null;
        }
        if (array_key_exists($employeeId, $cache)) {
            return $cache[$employeeId];
        }

        $employee = DB::selectOne('SELECT id, employee_code, full_name, email, photo, status FROM employees WHERE id = ?', [$employeeId]);
        if (! $employee || (int) $employee['status'] !== 1) {
            return $cache[$employeeId] = null;
        }

        $user = self::linkEmployee($employee);
        // First K-Academy page of this login: same as the old K-Academy login (last_login + activity log)
        if ($user['status'] === 'active' && session('ka_seen_user') !== $user['id']) {
            session(['ka_seen_user' => $user['id']]);
            self::db()->update('UPDATE users SET last_login = NOW() WHERE id = ?', [$user['id']]);
            self::log((int) $user['id'], 'login', ['via' => 'K-Connect']);
        }

        return $cache[$employeeId] = $user;
    }

    /** Find (or create) the K-Academy user for an employees row. */
    public static function linkEmployee(array $employee): array
    {
        $ka = self::db();
        $user = null;
        if (! empty($employee['employee_code'])) {
            $user = $ka->selectOne('SELECT * FROM users WHERE employee_code = ? ORDER BY id LIMIT 1', [$employee['employee_code']]);
        }
        if (! $user && ! empty($employee['email'])) {
            $user = $ka->selectOne('SELECT * FROM users WHERE email = ? ORDER BY id LIMIT 1', [$employee['email']]);
        }

        if (! $user) {
            // Random password: K-Academy is reached through the K-Connect login only.
            $ka->insert("INSERT INTO users (employee_code, full_name, email, password, role, status, email_verified, created_at)
                VALUES (?, ?, ?, ?, 'user', 'active', 1, NOW())", [
                $employee['employee_code'] ?: 'EMP-'.$employee['id'],
                $employee['full_name'],
                $employee['email'] ?: ($employee['employee_code'].'@kconnect.local'),
                password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT),
            ]);
            $user = $ka->selectOne('SELECT * FROM users WHERE id = ?', [$ka->getPdo()->lastInsertId()]);
            self::log($user['id'], 'account_linked', ['employee_id' => $employee['id']]);
        } elseif ($user['status'] === 'pending' || $user['full_name'] !== $employee['full_name']) {
            // Old self-registrations waited for approval: an active K-Connect employee needs none.
            // 'inactive' is kept: that is an admin switching off K-Academy for this person.
            $status = $user['status'] === 'pending' ? 'active' : $user['status'];
            $ka->update('UPDATE users SET status = ?, full_name = ? WHERE id = ?', [$status, $employee['full_name'], $user['id']]);
            $user['status'] = $status;
            $user['full_name'] = $employee['full_name'];
        }
        $user['employee_id'] = $employee['id'];
        $user['employee_photo'] = $employee['photo'] ?? null;

        return $user;
    }

    /** K-Academy user id of the learner, or stops with a redirect to the K-Connect login. */
    public static function requireLearner(): array
    {
        $user = self::learner();
        if (! $user) {
            kc_redirect(url('login'));
        }
        if ($user['status'] !== 'active') {
            throw new HttpException(403, 'Your K-Academy access has been switched off. Please contact HR or the K-Academy administrator.');
        }

        return $user;
    }

    /**
     * K-Academy user id to record as the acting admin (created_by, approved_by...).
     * Matches the K-Connect admin by e-mail, otherwise uses the first K-Academy admin account.
     */
    public static function adminUserId(): ?int
    {
        static $id = false;
        if ($id !== false) {
            return $id;
        }
        $ka = self::db();
        $email = null;
        if (session('admin_id')) {
            $email = DB::selectOne('SELECT email FROM admin_users WHERE id = ?', [session('admin_id')])['email'] ?? null;
        }
        $row = $email ? $ka->selectOne("SELECT id FROM users WHERE email = ? AND role = 'admin' LIMIT 1", [$email]) : null;
        $row = $row ?: $ka->selectOne("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1");

        return $id = $row ? (int) $row['id'] : null;
    }

    /** Admin pages: K-Connect admin login. */
    public static function requireAdmin(): void
    {
        if (! isAdminLoggedIn()) {
            kc_redirect(url('admin/login'));
        }
    }

    public static function log(?int $userId, string $action, array $details = []): void
    {
        try {
            self::db()->insert('INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)', [
                $userId, $action, json_encode($details), request()->ip(), substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function notify(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        self::db()->insert('INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)', [$userId, $title, $message, $type, $link]);
    }

    /**
     * A learner finished every lesson and passed the quiz: record a pending certificate (once)
     * and tell the K-Academy admins, as generate-certificate did. Returns the certificate id.
     */
    public static function requestCertificate(int $userId, int $courseId, ?int $quizResultId = null): int
    {
        $ka = self::db();
        $existing = $ka->selectOne('SELECT id FROM certificates WHERE user_id = ? AND course_id = ?', [$userId, $courseId]);
        if ($existing) {
            return (int) $existing['id'];
        }
        $number = ka_generate_certificate_number();
        $ka->insert("INSERT INTO certificates (certificate_number, user_id, course_id, quiz_result_id, issued_date, status) VALUES (?, ?, ?, ?, CURDATE(), 'pending')",
            [$number, $userId, $courseId, $quizResultId]);
        $id = (int) $ka->getPdo()->lastInsertId();

        $who = $ka->selectOne('SELECT full_name FROM users WHERE id = ?', [$userId])['full_name'] ?? 'A learner';
        $course = $ka->selectOne('SELECT title FROM courses WHERE id = ?', [$courseId])['title'] ?? '';
        foreach ($ka->select("SELECT id FROM users WHERE role = 'admin'") as $admin) {
            self::notify((int) $admin['id'], 'Certificate Approval Required', "New certificate pending approval for {$who} - Course: {$course}", 'warning', "admin/approve-certificate.php?id={$id}");
        }
        self::log($userId, 'requested_certificate', ['course_id' => $courseId, 'certificate_number' => $number, 'status' => 'pending']);

        return $id;
    }

    /** Public URL of a K-Academy file (courses/, attachments/, videos/, profiles/). */
    public static function fileUrl(string $sub, ?string $file): ?string
    {
        if ($file === null || $file === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $file)) {
            return $file;
        }

        return asset(self::UPLOAD_DIR.'/'.$sub.'/'.ltrim($file, '/'));
    }

    public static function filePath(string $sub, string $file = ''): string
    {
        return public_path(self::UPLOAD_DIR.'/'.$sub.($file !== '' ? '/'.$file : ''));
    }

    /**
     * How to show a lesson or course video. lessons.video_type is 'youtube' or 'upload' (unchanged
     * column); any non-YouTube link stored as 'youtube' is treated as an external link.
     *
     * @return array{kind: string, src: ?string, embed: ?string}
     *   kind: youtube | vimeo | file | drive | link | none
     */
    public static function video(?string $url, ?string $type = null): array
    {
        // Links were saved through htmlspecialchars() (&amp; in query strings)
        $url = trim(html_entity_decode((string) $url, ENT_QUOTES));
        if ($url === '') {
            return ['kind' => 'none', 'src' => null, 'embed' => null];
        }
        if ($type === 'upload' && ! preg_match('#^https?://#i', $url)) {
            return ['kind' => 'file', 'src' => self::fileUrl('videos', $url), 'embed' => null];
        }
        if ($id = self::youtubeId($url)) {
            return ['kind' => 'youtube', 'src' => $url, 'embed' => 'https://www.youtube.com/embed/'.$id.'?rel=0&enablejsapi=1', 'id' => $id];
        }
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#i', $url, $m)) {
            return ['kind' => 'vimeo', 'src' => $url, 'embed' => 'https://player.vimeo.com/video/'.$m[1]];
        }
        if (preg_match('#drive\.google\.com/file/d/([^/?]+)#i', $url, $m)) {
            return ['kind' => 'drive', 'src' => $url, 'embed' => 'https://drive.google.com/file/d/'.$m[1].'/preview'];
        }
        if (preg_match('#\.(mp4|webm|ogg|m3u8)(\?.*)?$#i', $url)) {
            return ['kind' => 'file', 'src' => $url, 'embed' => null];
        }

        return ['kind' => 'link', 'src' => $url, 'embed' => $url];
    }

    public static function youtubeId(string $url): ?string
    {
        if (preg_match('#(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})#i', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    /** Learner pages (same names as the old kacademy/*.php files). */
    public const LEARNER_PAGES = [
        'index', 'dashboard', 'browse-courses', 'browse-course', 'course-overview', 'course-player', 'course-notes',
        'course-discussions', 'course-resources', 'my-courses', 'progress', 'notifications', 'quiz', 'quiz-results',
        'certificates', 'certificate-pending', 'download-certificate', 'generate-certificate', 'generate_certificate',
        'verify-certificate', 'view-certificate', 'profile', 'change-password', 'settings',
    ];

    /**
     * Old K-Academy address ('/admin/manage_courses.php', 'course-player.php?course_id=1',
     * '../dashboard.php') to the address of the same page here (/academy/..., /academy/admin/...).
     * Relative addresses are read from the folder of the current page, as the browser did.
     */
    public static function pageUrl(string $url): string
    {
        if (preg_match('#^(https?:)?//#i', $url)) {
            return $url;
        }
        $query = '';
        if (preg_match('/^([^?#]*)([?#].*)$/s', $url, $m)) {
            [$url, $query] = [$m[1], $m[2]];
        }
        if (str_starts_with($url, '/')) {
            $base = 'academy';
        } else {
            $current = trim(request()->path(), '/');
            $base = preg_match('#^academy/(admin|api)(/|$)#', $current, $m2) ? 'academy/'.$m2[1] : 'academy';
        }
        $parts = [];
        foreach (explode('/', $base.'/'.$url) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                if (count($parts) > 1) {
                    array_pop($parts);
                }
                continue;
            }
            $parts[] = $seg;
        }
        $last = preg_replace('/\.php$/i', '', (string) array_pop($parts));
        if ($last !== '' && $last !== 'academy') {
            $parts[] = $last;
        }
        $path = implode('/', $parts);
        if ($path === 'academy/view-certificate') {
            $path = 'academy/certificates';
        }

        return url($path ?: 'academy').$query;
    }

    /** Old K-Academy links stored in the database (notifications.link) to the new addresses. */
    public static function mapOldLink(?string $link): ?string
    {
        $link = trim((string) $link);
        if ($link === '') {
            return null;
        }
        if ($link === '#' || str_starts_with($link, '#')) {
            return $link;
        }
        if (preg_match('#^https?://([^/]+)(.*)$#i', $link, $m)) {
            if (! str_contains(strtolower($m[1]), 'kacademy')) {
                return $link;
            }
            $link = $m[2] ?: '/';
        }
        $query = '';
        if (preg_match('/^([^?#]*)([?#].*)$/s', $link, $m)) {
            [$link, $query] = [$m[1], $m[2]];
        }
        $page = preg_replace('/\.php$/i', '', basename($link));
        $isAdmin = (bool) preg_match('#(^|/)admin/#', $link) && ! in_array($page, self::LEARNER_PAGES, true);
        if ($page === 'view-certificate') {
            $page = 'certificates';
        }
        if ($page === '' || $page === 'index' && ! $isAdmin) {
            $page = 'dashboard';
        }

        return url(($isAdmin ? 'academy/admin/' : 'academy/').$page).$query;
    }

    /** Course progress in percent from lesson_progress (same rule as the old calculateCourseProgress). */
    public static function courseProgress(int $userId, int $courseId): float
    {
        $ka = self::db();
        $total = (int) ($ka->selectOne('SELECT COUNT(*) c FROM lessons WHERE course_id = ?', [$courseId])['c'] ?? 0);
        if ($total === 0) {
            return 0.0;
        }
        $done = (int) ($ka->selectOne('SELECT COUNT(*) c FROM lesson_progress WHERE user_id = ? AND course_id = ? AND completed = 1', [$userId, $courseId])['c'] ?? 0);

        return round($done / $total * 100, 2);
    }
}
