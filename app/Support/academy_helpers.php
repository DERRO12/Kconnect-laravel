<?php

/*
|--------------------------------------------------------------------------
| K-Academy helper functions (ported from kacademy/config/functions.php,
| config/auth.php and includes/functions.php)
|--------------------------------------------------------------------------
| Same behaviour as the old K-Academy site. They carry a ka_ prefix so they never clash
| with the K-Connect helpers of the same name (sanitize, isLoggedIn, timeAgo...).
| The K-Academy pages are in app/Http/Controllers/Academy and resources/views/academy.
*/

use App\Support\Academy;

const KA_MAX_FILE_SIZE = 52428800;
const KA_ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'webm'];

if (! function_exists('ka_app_url')) {
    /** Old APP_URL: the K-Academy home address (e.g. https://kconnect.kibokogroup.com/academy). */
    function ka_app_url(): string
    {
        return url('academy');
    }
}

if (! function_exists('ka_upload_path')) {
    /** Old UPLOAD_PATH: folder of K-Academy uploads on disk, with a trailing slash. */
    function ka_upload_path(): string
    {
        return rtrim(Academy::filePath(''), '/').'/';
    }
}

if (! function_exists('ka_link')) {
    /** A link stored by the old site (notifications.link...) as an address of this site. */
    function ka_link(?string $link): string
    {
        return Academy::mapOldLink($link) ?? '#';
    }
}

if (! function_exists('ka_redirect')) {
    /**
     * Old redirect($url, $message, $type): optional flash message, then go to the K-Academy page.
     * '/admin/manage_courses.php' and 'course-player.php?x=1' are old K-Academy addresses.
     * Returns the redirect: the page code does "return ka_redirect(...)".
     */
    function ka_redirect(string $url, ?string $message = null, string $type = 'info'): \Illuminate\Http\Response
    {
        if ($message) {
            ka_flash($message, $type);
        }

        return kc_redirect_response(Academy::pageUrl($url));
    }
}

if (! function_exists('ka_flash')) {
    function ka_flash(string $message, string $type = 'info'): void
    {
        session()->flash('ka_flash_message', $message);
        session()->flash('ka_flash_type', $type);
    }
}

if (! function_exists('ka_flash_html')) {
    /** Old displayFlashMessage() */
    function ka_flash_html(): string
    {
        $message = session()->pull('ka_flash_message');
        $type = session()->pull('ka_flash_type', 'info');
        if (! $message) {
            return '';
        }
        $icons = [
            'success' => 'check-circle',
            'danger' => 'exclamation-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle',
        ];

        return sprintf(
            '<div class="alert alert-%s alert-dismissible fade show" role="alert">
                <i class="bi bi-%s me-2"></i> %s
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>',
            e($type), $icons[$type] ?? 'info-circle', e($message)
        );
    }
}

if (! function_exists('ka_verify_csrf')) {
    /** Old verifyCSRFToken(): the token is now Laravel's CSRF token (csrf_token()). */
    function ka_verify_csrf($token): bool
    {
        return is_string($token) && $token !== '' && hash_equals(csrf_token(), $token);
    }
}

if (! function_exists('ka_sanitize')) {
    function ka_sanitize($input): string
    {
        if (is_array($input) || is_object($input)) {
            return '';
        }

        return htmlspecialchars(strip_tags(trim((string) $input)), ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('ka_log_activity')) {
    function ka_log_activity($pdo, $userId, $action, $details = []): bool
    {
        Academy::log($userId ? (int) $userId : null, (string) $action, (array) $details);

        return true;
    }
}

if (! function_exists('ka_notify')) {
    /** Old createNotification() */
    function ka_notify($pdo, $userId, $title, $message, $type = 'info', $link = null): bool
    {
        try {
            Academy::notify((int) $userId, (string) $title, (string) $message, (string) $type, $link);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}

if (! function_exists('ka_time_ago')) {
    function ka_time_ago($datetime): string
    {
        $time = strtotime((string) $datetime);
        if ($time === false) {
            return '';
        }
        $diff = time() - $time;
        if ($diff < 60) {
            return $diff.' seconds ago';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);

            return $mins.' minute'.($mins > 1 ? 's' : '').' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);

            return $hours.' hour'.($hours > 1 ? 's' : '').' ago';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);

            return $days.' day'.($days > 1 ? 's' : '').' ago';
        }

        return date('M d, Y', $time);
    }
}

if (! function_exists('ka_format_file_size')) {
    function ka_format_file_size($bytes): string
    {
        $bytes = (float) $bytes;
        if ($bytes <= 0) {
            return '0 Bytes';
        }
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes) / log($k));
        $i = min($i, count($sizes) - 1);

        return round($bytes / pow($k, $i), 2).' '.$sizes[$i];
    }
}

if (! function_exists('ka_file_icon')) {
    /** Bootstrap icon class for a file extension (old getFileIcon() in config/functions.php). */
    function ka_file_icon($extension): string
    {
        $icons = [
            'pdf' => 'bi-file-pdf', 'doc' => 'bi-file-word', 'docx' => 'bi-file-word',
            'xls' => 'bi-file-excel', 'xlsx' => 'bi-file-excel', 'ppt' => 'bi-file-ppt', 'pptx' => 'bi-file-ppt',
            'jpg' => 'bi-file-image', 'jpeg' => 'bi-file-image', 'png' => 'bi-file-image', 'gif' => 'bi-file-image',
            'mp4' => 'bi-file-play', 'zip' => 'bi-file-zip', 'txt' => 'bi-file-text',
        ];

        return $icons[strtolower((string) $extension)] ?? 'bi-file';
    }
}

if (! function_exists('ka_validate_employee_code')) {
    function ka_validate_employee_code($code): bool
    {
        return (bool) preg_match('/^(KEL|EXP)-\d{4}$/', (string) $code);
    }
}

if (! function_exists('ka_generate_certificate_number')) {
    function ka_generate_certificate_number($pdo = null): string
    {
        $ka = Academy::db();
        do {
            $number = 'CERT-'.date('Y').'-'.str_pad((string) mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while ($ka->selectOne('SELECT id FROM certificates WHERE certificate_number = ?', [$number]));

        return $number;
    }
}

if (! function_exists('ka_calculate_course_progress')) {
    /** Old calculateCourseProgress(): recalculates and saves enrollments.progress_percentage. */
    function ka_calculate_course_progress($pdo, $userId, $courseId): float
    {
        try {
            $ka = Academy::db();
            $total = (int) ($ka->selectOne('SELECT COUNT(*) c FROM lessons WHERE course_id = ?', [$courseId])['c'] ?? 0);
            if ($total === 0) {
                return 0;
            }
            $done = (int) ($ka->selectOne('SELECT COUNT(*) c FROM lesson_progress WHERE user_id = ? AND course_id = ? AND completed = 1', [$userId, $courseId])['c'] ?? 0);
            $percentage = ($done / $total) * 100;
            $ka->update("UPDATE enrollments
                SET progress_percentage = ?,
                    status = CASE WHEN ? >= 100 THEN 'completed' ELSE 'in_progress' END,
                    completed_at = CASE WHEN ? >= 100 THEN NOW() ELSE completed_at END,
                    last_accessed = NOW()
                WHERE user_id = ? AND course_id = ?", [$percentage, $percentage, $percentage, $userId, $courseId]);

            return round($percentage, 2);
        } catch (\Throwable $e) {
            report($e);

            return 0;
        }
    }
}

if (! function_exists('ka_is_enrolled')) {
    function ka_is_enrolled($pdo, $userId, $courseId): bool
    {
        return (bool) Academy::db()->selectOne('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?', [$userId, $courseId]);
    }
}

if (! function_exists('ka_user_course_progress')) {
    /** Old getUserCourseProgress() */
    function ka_user_course_progress($pdo, $userId, $courseId): float
    {
        $value = Academy::db()->selectOne('SELECT progress_percentage FROM enrollments WHERE user_id = ? AND course_id = ?', [$userId, $courseId])['progress_percentage'] ?? 0;

        return $value ? round((float) $value, 2) : 0;
    }
}

if (! function_exists('ka_is_lesson_completed')) {
    function ka_is_lesson_completed($pdo, $userId, $lessonId): bool
    {
        return (bool) (Academy::db()->selectOne('SELECT completed FROM lesson_progress WHERE user_id = ? AND lesson_id = ?', [$userId, $lessonId])['completed'] ?? false);
    }
}

if (! function_exists('ka_next_lesson')) {
    /** Old getNextLesson(): id of the next lesson of the course, or null. */
    function ka_next_lesson($pdo, $courseId, $currentLessonId)
    {
        return Academy::db()->selectOne('SELECT id FROM lessons WHERE course_id = ? AND order_number > (SELECT order_number FROM lessons WHERE id = ?) ORDER BY order_number ASC LIMIT 1', [$courseId, $currentLessonId])['id'] ?? null;
    }
}

if (! function_exists('ka_slug')) {
    /** Old generateSlug() */
    function ka_slug($string): string
    {
        return trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower((string) $string)), '-');
    }
}

if (! function_exists('ka_can_take_quiz')) {
    function ka_can_take_quiz($pdo, $userId, $quizId): bool
    {
        $ka = Academy::db();
        $allowed = $ka->selectOne('SELECT attempts_allowed FROM quizzes WHERE id = ?', [$quizId])['attempts_allowed'] ?? null;
        if (! $allowed) {
            return true; // unlimited attempts
        }
        $used = (int) ($ka->selectOne('SELECT COUNT(*) c FROM quiz_results WHERE user_id = ? AND quiz_id = ?', [$userId, $quizId])['c'] ?? 0);

        return $used < $allowed;
    }
}

if (! function_exists('ka_upload_file')) {
    /**
     * Old uploadFile(): validates the extension and size of one $_FILES entry and saves it with
     * a unique name. Returns ['success' => true, 'filename' => ...] or ['success' => false, 'errors' => [...]].
     */
    function ka_upload_file($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], $maxSize = 5242880): array
    {
        $errors = [];
        if (! is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            ];
            $errors[] = $uploadErrors[$file['error'] ?? UPLOAD_ERR_NO_FILE] ?? 'Unknown upload error';

            return ['success' => false, 'errors' => $errors];
        }
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'errors' => ['File size exceeds limit of '.ka_format_file_size($maxSize)]];
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (! in_array($ext, $allowedTypes, true)) {
            return ['success' => false, 'errors' => ['File type not allowed. Allowed types: '.implode(', ', $allowedTypes)]];
        }

        $filename = uniqid().'_'.time().'.'.$ext;
        $targetPath = rtrim((string) $targetDir, '/').'/'.$filename;
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        if (is_uploaded_file($file['tmp_name']) ? move_uploaded_file($file['tmp_name'], $targetPath) : @rename($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $filename, 'path' => $targetPath, 'size' => $file['size'], 'type' => $ext];
        }

        return ['success' => false, 'errors' => ['Failed to move uploaded file']];
    }
}

if (! function_exists('ka_ensure_dir')) {
    function ka_ensure_dir($path): void
    {
        if (! file_exists($path)) {
            @mkdir($path, 0775, true);
        }
    }
}

if (! function_exists('ka_course_thumbnail_url')) {
    /** Old getCourseThumbnailUrl(): the course image, or a coloured placeholder. */
    function ka_course_thumbnail_url($thumbnail, $title = 'Course', $category = 'default'): string
    {
        $colors = ['foundation' => '4299e1', 'mastery' => '48bb78', 'compliance' => '9f7aea', 'resource' => 'ed8936', 'default' => '009ada'];
        $color = $colors[$category] ?? $colors['default'];
        if (! empty($thumbnail) && (preg_match('#^https?://#i', $thumbnail) || file_exists(Academy::filePath('courses', $thumbnail)))) {
            return Academy::fileUrl('courses', $thumbnail);
        }

        return "https://placehold.co/800x600/{$color}/white?text=".urlencode((string) $title);
    }
}

if (! function_exists('ka_course_thumbnail')) {
    /** Landing page course image: ['exists' => bool, 'url' => image or coloured placeholder]. */
    function ka_course_thumbnail(array $course): array
    {
        $colors = ['foundation' => '4299e1', 'mastery' => '48bb78', 'compliance' => '9f7aea', 'resource' => 'ed8936', 'default' => '009ada'];
        $category = $course['category_type'] ?? 'default';
        $color = $colors[$category] ?? $colors['default'];
        $thumb = $course['thumbnail'] ?? '';
        if ($thumb !== '' && (preg_match('#^https?://#i', $thumb) || file_exists(Academy::filePath('courses', $thumb)))) {
            return ['exists' => true, 'url' => Academy::fileUrl('courses', $thumb)];
        }

        return ['exists' => false, 'url' => "https://placehold.co/600x400/{$color}/white?text=".urlencode(ucfirst($category).'+Course')];
    }
}

if (! function_exists('ka_youtube_id')) {
    function ka_youtube_id($url): string
    {
        return Academy::youtubeId((string) $url) ?? '';
    }
}

if (! function_exists('ka_validate_file_upload')) {
    /** Old validateFileUpload(): ['valid' => bool, 'extension' => ..., 'error' => ...] */
    function ka_validate_file_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], $max_size = 5242880): array
    {
        if (! is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Upload failed'];
        }
        if ($file['size'] > $max_size) {
            return ['valid' => false, 'error' => 'File too large'];
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (! in_array($ext, $allowed_types, true)) {
            return ['valid' => false, 'error' => 'File type not allowed'];
        }

        return ['valid' => true, 'extension' => $ext];
    }
}
