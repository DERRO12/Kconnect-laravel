<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Support\Academy;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use PDO;
use PDOException;

/**
 * K-Academy admin pages (were kacademy/admin/*.php). K-Connect admin login.
 * The page code is the old K-Academy code on the "kacademy" database connection; it uses
 * $pdo (Academy::pdo()), $user_id/$user (Academy::pageVars()) and the ka_* helpers.
 */
class AdminController extends Controller
{
    /** academy/admin/dashboard (was admin/dashboard.php) */
    public function dashboard(Request $request)
    {
        extract(Academy::pageVars(true, 'dashboard'));

        // Get statistics
        $stats = [];

        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $stats['total_users'] = $stmt->fetchColumn();

        // Pending approvals
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'");
        $stats['pending_users'] = $stmt->fetchColumn();

        // Total courses
        $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
        $stats['total_courses'] = $stmt->fetchColumn();

        // Total enrollments
        $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments");
        $stats['total_enrollments'] = $stmt->fetchColumn();

        // Completion rate
        $stmt = $pdo->query("
            SELECT 
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(*) as total
            FROM enrollments
        ");
        $result = $stmt->fetch();
        $stats['completion_rate'] = $result['total'] > 0 ? round(($result['completed'] / $result['total']) * 100) : 0;

        // Recent activities
        $stmt = $pdo->query("
            SELECT al.*, u.full_name, u.employee_code 
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC 
            LIMIT 10
        ");
        $recent_activities = $stmt->fetchAll();

        // Course enrollment statistics
        $stmt = $pdo->query("
            SELECT c.title, COUNT(e.id) as enrollments,
                   COUNT(CASE WHEN e.status = 'completed' THEN 1 END) as completed
            FROM courses c
            LEFT JOIN enrollments e ON c.id = e.course_id
            GROUP BY c.id
            ORDER BY enrollments DESC
            LIMIT 5
        ");
        $course_stats = $stmt->fetchAll();

        return view('academy.admin.dashboard', get_defined_vars());
    }

    /** academy/admin/manage_courses (was admin/manage_courses.php) */
    public function manageCourses(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_courses'));

        // Handle course deletion
        if (isset($_GET['delete']) && ka_verify_csrf($_GET['token'] ?? '')) {
            $courseId = (int)$_GET['delete'];

            // Delete course (lessons will be deleted via cascade)
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            if ($stmt->execute([$courseId])) {
                ka_log_activity($pdo, $user_id, 'deleted_course', ['course_id' => $courseId]);
                return ka_redirect('/admin/manage_courses.php', 'Course deleted successfully', 'success');
            }
        }

        // Get all courses with statistics
        $stmt = $pdo->query("
            SELECT c.*, 
                   COUNT(DISTINCT l.id) as lesson_count,
                   COUNT(DISTINCT e.id) as enrollment_count,
                   u.full_name as created_by_name
            FROM courses c
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN enrollments e ON c.id = e.course_id
            LEFT JOIN users u ON c.created_by = u.id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $courses = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.admin.manage_courses', get_defined_vars());
    }

    /** academy/admin/add_course (was admin/add_course.php) */
    public function addCourse(Request $request)
    {
        extract(Academy::pageVars(true, 'add_course'));

        $error = '';
        $success = '';

        // Only these four categories - matching the index.php
        $categories = [
            'foundation' => 'K-Start: The Foundation',
            'mastery' => 'K-Expert: The Mastery',
            'resource' => 'K-Library: The Resource',
            'compliance' => 'K-Compliance Center'
        ];

        // Color mapping for UI
        $category_colors = [
            'foundation' => '#4299e1',
            'mastery' => '#48bb78',
            'resource' => '#ed8936',
            'compliance' => '#9f7aea'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $title = ka_sanitize($_POST['title'] ?? '');
                $slug = ka_sanitize(strtolower(str_replace(' ', '-', $title)));
                $description = $_POST['description'] ?? '';

                // Get the selected category type (this is the main category)
                $category_type = ka_sanitize($_POST['category_type'] ?? '');

                // The category display name from the selected type
                $category = $categories[$category_type] ?? '';

                $level = ka_sanitize($_POST['level'] ?? 'beginner');
                $duration = ka_sanitize($_POST['duration'] ?? '');
                $status = ka_sanitize($_POST['status'] ?? 'draft');

                // Validate that a category was selected
                if (empty($category_type) || !isset($categories[$category_type])) {
                    $error = 'Please select a category';
                }

                // Handle thumbnail upload
                $thumbnail = '';
                if (empty($error) && isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $thumbnail = uniqid() . '.' . $ext;
                        $uploadPath = ka_upload_path() . 'courses/' . $thumbnail;

                        if (!is_dir(ka_upload_path() . 'courses/')) {
                            mkdir(ka_upload_path() . 'courses/', 0777, true);
                        }

                        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadPath)) {
                            $error = 'Failed to upload thumbnail';
                        }
                    } else {
                        $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif';
                    }
                }

                if (empty($error)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO courses (title, slug, description, thumbnail, category, category_type, level, duration, status, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");

                    if ($stmt->execute([$title, $slug, $description, $thumbnail, $category, $category_type, $level, $duration, $status, $user_id])) {
                        $courseId = $pdo->lastInsertId();
                        ka_log_activity($pdo, $user_id, 'created_course', ['course_id' => $courseId, 'title' => $title]);
                        return ka_redirect('/admin/manage_lessons.php?course_id=' . $courseId, 'Course created successfully. Now add lessons.', 'success');
                    } else {
                        $error = 'Failed to create course';
                    }
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.admin.add_course', get_defined_vars());
    }

    /** academy/admin/edit_course (was admin/edit_course.php) */
    public function editCourse(Request $request)
    {
        extract(Academy::pageVars(true, 'edit_course'));

        $page_title = 'Edit Course';
        $is_admin_area = true;

        $error = '';
        $success = '';

        // Get course ID from URL
        $course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$course_id) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid course ID', 'danger');
        }

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/admin/manage_courses.php', 'Course not found', 'danger');
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $title = ka_sanitize($_POST['title'] ?? '');
                $slug = ka_sanitize($_POST['slug'] ?? '');
                $description = $_POST['description'] ?? '';
                $category = ka_sanitize($_POST['category'] ?? '');
                $level = ka_sanitize($_POST['level'] ?? 'beginner');
                $duration = ka_sanitize($_POST['duration'] ?? '');
                $status = ka_sanitize($_POST['status'] ?? 'draft');

                // Validate required fields
                if (empty($title)) {
                    $error = 'Course title is required';
                } else {
                    // Generate slug if empty
                    if (empty($slug)) {
                        $slug = ka_slug($title);
                    }

                    // Check if slug exists for other courses
                    $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ? AND id != ?");
                    $stmt->execute([$slug, $course_id]);
                    if ($stmt->fetch()) {
                        $slug = $slug . '-' . uniqid();
                    }

                    // Handle thumbnail upload
                    $thumbnail = $course['thumbnail']; // Keep existing by default

                    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                        $upload_result = ka_upload_file(
                            $_FILES['thumbnail'], 
                            ka_upload_path() . 'courses/',
                            ['jpg', 'jpeg', 'png', 'gif'],
                            2097152 // 2MB
                        );

                        if ($upload_result['success']) {
                            // Delete old thumbnail if exists
                            if ($thumbnail && file_exists(ka_upload_path() . 'courses/' . $thumbnail)) {
                                unlink(ka_upload_path() . 'courses/' . $thumbnail);
                            }
                            $thumbnail = $upload_result['filename'];
                        } else {
                            $error = implode(', ', $upload_result['errors']);
                        }
                    }

                    if (empty($error)) {
                        // Update course
                        $stmt = $pdo->prepare("
                            UPDATE courses 
                            SET title = ?, slug = ?, description = ?, thumbnail = ?, 
                                category = ?, level = ?, duration = ?, status = ?
                            WHERE id = ?
                        ");

                        if ($stmt->execute([$title, $slug, $description, $thumbnail, $category, $level, $duration, $status, $course_id])) {
                            ka_log_activity($pdo, $user_id, 'updated_course', ['course_id' => $course_id, 'title' => $title]);

                            // Update session data
                            $course['title'] = $title;
                            $course['slug'] = $slug;
                            $course['description'] = $description;
                            $course['category'] = $category;
                            $course['level'] = $level;
                            $course['duration'] = $duration;
                            $course['status'] = $status;
                            $course['thumbnail'] = $thumbnail;

                            $success = 'Course updated successfully';
                        } else {
                            $error = 'Failed to update course';
                        }
                    }
                }
            }
        }

        $csrf_token = csrf_token();

        // Include header and sidebar

        return view('academy.admin.edit_course', get_defined_vars());
    }

    /** academy/admin/manage_lessons (was admin/manage_lessons.php) */
    public function manageLessons(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_lessons'));

        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/admin/manage_courses.php', 'Course not found', 'danger');
        }

        // Handle lesson deletion
        if (isset($_GET['delete']) && ka_verify_csrf($_GET['token'] ?? '')) {
            $lessonId = (int)$_GET['delete'];

            // Get lesson details to delete video and attachments
            $stmt = $pdo->prepare("SELECT video_url, video_type, attachments FROM lessons WHERE id = ? AND course_id = ?");
            $stmt->execute([$lessonId, $course_id]);
            $lesson = $stmt->fetch();

            if ($lesson) {
                // Delete video file if uploaded
                if ($lesson['video_type'] === 'upload' && $lesson['video_url']) {
                    $video_path = ka_upload_path() . 'videos/' . $lesson['video_url'];
                    if (file_exists($video_path)) {
                        unlink($video_path);
                    }
                }

                // Delete attachment files
                if ($lesson['attachments']) {
                    $attachments = json_decode($lesson['attachments'], true);
                    foreach ($attachments as $attachment) {
                        $attachment_path = ka_upload_path() . 'attachments/' . $attachment;
                        if (file_exists($attachment_path)) {
                            unlink($attachment_path);
                        }
                    }
                }
            }

            $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ? AND course_id = ?");
            if ($stmt->execute([$lessonId, $course_id])) {
                ka_log_activity($pdo, $user_id, 'deleted_lesson', ['lesson_id' => $lessonId, 'course_id' => $course_id]);
                return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson deleted successfully', 'success');
            }
        }

        // Get lessons for this course
        $stmt = $pdo->prepare("
            SELECT l.*, 
                   COUNT(lp.id) as completions
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id
            WHERE l.course_id = ?
            GROUP BY l.id
            ORDER BY l.order_number
        ");
        $stmt->execute([$course_id]);
        $lessons = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.admin.manage_lessons', get_defined_vars());
    }

    /** academy/admin/add_lesson (was admin/add_lesson.php) */
    public function addLesson(Request $request)
    {
        extract(Academy::pageVars(true, 'add_lesson'));

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ka_redirect('/admin/manage_courses.php', 'Invalid request method', 'danger');
        }

        // Verify CSRF token
        if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid security token', 'danger');
        }

        $course_id = (int)($_POST['course_id'] ?? 0);
        $title = ka_sanitize($_POST['title'] ?? '');
        $video_type = $_POST['video_type'] ?? 'youtube';
        $video_url = ka_sanitize($_POST['video_url'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $content = $_POST['content'] ?? '';

        // Validate required fields
        if (empty($title)) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson title is required', 'danger');
        }

        if (empty($course_id)) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid course ID', 'danger');
        }

        // Verify course exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            return ka_redirect('/admin/manage_courses.php', 'Course not found', 'danger');
        }

        // Handle video upload if type is upload
        $video_filename = '';
        if ($video_type === 'upload' && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $upload_result = ka_upload_file(
                $_FILES['video_file'],
                ka_upload_path() . 'videos/',
                ['mp4', 'webm', 'ogg', 'm4v', 'mov'],
                config('academy.max_video_mb') * 1048576
            );

            if ($upload_result['success']) {
                $video_filename = $upload_result['filename'];
            } else {
                return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 
                         'Video upload failed: ' . implode(', ', $upload_result['errors']), 
                         'danger');
            }
        } elseif ($video_type === 'youtube' && empty($video_url)) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Video link is required', 'danger');
        } elseif ($video_type === 'youtube' && !preg_match('#^https?://#i', $video_url)) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'The video link must start with http:// or https://', 'danger');
        } elseif ($video_type === 'upload' && empty($_FILES['video_file']['name'])) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Choose a video file to upload', 'danger');
        }

        // Handle attachments
        $attachments = [];
        if (isset($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $upload_result = ka_upload_file(
                        $file,
                        ka_upload_path() . 'attachments/',
                        ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'],
                        10485760 // 10MB
                    );

                    if ($upload_result['success']) {
                        $attachments[] = $upload_result['filename'];
                    }
                }
            }
        }

        // Get the highest order number for this course
        $stmt = $pdo->prepare("SELECT MAX(order_number) FROM lessons WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $max_order = $stmt->fetchColumn();
        $order_number = $max_order ? $max_order + 1 : 1;

        // Insert lesson
        $stmt = $pdo->prepare("
            INSERT INTO lessons (course_id, title, content, video_url, video_type, duration, order_number, attachments) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $video_final_url = ($video_type === 'youtube') ? $video_url : $video_filename;
        $attachments_json = !empty($attachments) ? json_encode($attachments) : null;

        if ($stmt->execute([$course_id, $title, $content, $video_final_url, $video_type, $duration, $order_number, $attachments_json])) {
            $lesson_id = $pdo->lastInsertId();

            // Log activity
            ka_log_activity($pdo, $user_id, 'added_lesson', [
                'lesson_id' => $lesson_id,
                'course_id' => $course_id,
                'title' => $title
            ]);

            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson added successfully', 'success');
        } else {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Failed to add lesson', 'danger');
        }

        return response('');
    }

    /** academy/admin/bulk_add_lessons: upload several videos at once, one lesson per file (new). */
    public function bulkAddLessons(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_lessons'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ka_redirect('/admin/manage_courses.php', 'Invalid request method', 'danger');
        }
        if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid security token', 'danger');
        }

        $course_id = (int)($_POST['course_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            return ka_redirect('/admin/manage_courses.php', 'Course not found', 'danger');
        }

        $files = $_FILES['video_files'] ?? null;
        if (!$files || !is_array($files['name']) || count(array_filter($files['name'])) === 0) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Choose one or more video files to upload', 'danger');
        }

        // Upload in the order the files were picked, sorted by name so "01 Intro", "02 ..." keep their order
        $order = array_keys($files['name']);
        usort($order, fn ($a, $b) => strnatcasecmp($files['name'][$a], $files['name'][$b]));

        $stmt = $pdo->prepare("SELECT MAX(order_number) FROM lessons WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $order_number = (int)$stmt->fetchColumn();

        $insert = $pdo->prepare("
            INSERT INTO lessons (course_id, title, content, video_url, video_type, duration, order_number, attachments)
            VALUES (?, ?, '', ?, 'upload', 0, ?, NULL)
        ");
        $added = 0;
        $failed = [];
        foreach ($order as $i) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
            $upload_result = ka_upload_file(
                $file,
                ka_upload_path() . 'videos/',
                ['mp4', 'webm', 'ogg', 'm4v', 'mov'],
                config('academy.max_video_mb') * 1048576
            );
            if (!$upload_result['success']) {
                $failed[] = $file['name'] . ' (' . implode(', ', $upload_result['errors']) . ')';
                continue;
            }
            $title = trim(preg_replace('/[_\s]+/', ' ', pathinfo($file['name'], PATHINFO_FILENAME)));
            $title = mb_substr($title !== '' ? $title : 'Lesson ' . ($order_number + 1), 0, 200);
            $order_number++;
            $insert->execute([$course_id, ka_sanitize($title), $upload_result['filename'], $order_number]);
            ka_log_activity($pdo, $user_id, 'added_lesson', [
                'lesson_id' => $pdo->lastInsertId(),
                'course_id' => $course_id,
                'title' => $title,
            ]);
            $added++;
        }

        $message = $added . ' lesson' . ($added === 1 ? '' : 's') . ' added from the uploaded videos.';
        if ($failed) {
            $message .= ' Not uploaded: ' . implode('; ', $failed);
        }
        return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, $message, $failed ? ($added ? 'warning' : 'danger') : 'success');
    }

    /** academy/admin/edit_lesson (was admin/edit_lesson.php) */
    public function editLesson(Request $request)
    {
        extract(Academy::pageVars(true, 'edit_lesson'));

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ka_redirect('/admin/manage_courses.php', 'Invalid request method', 'danger');
        }

        // Verify CSRF token
        if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid security token', 'danger');
        }

        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        $title = ka_sanitize($_POST['title'] ?? '');
        $video_type = $_POST['video_type'] ?? 'youtube';
        $video_url = ka_sanitize($_POST['video_url'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $content = $_POST['content'] ?? '';

        // Validate required fields
        if (empty($title)) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson title is required', 'danger');
        }

        if (empty($lesson_id) || empty($course_id)) {
            return ka_redirect('/admin/manage_courses.php', 'Invalid lesson or course ID', 'danger');
        }

        // Get current lesson data
        $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ? AND course_id = ?");
        $stmt->execute([$lesson_id, $course_id]);
        $lesson = $stmt->fetch();

        if (!$lesson) {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson not found', 'danger');
        }

        // Handle video
        $video_final_url = $lesson['video_url']; // Keep existing by default

        if ($video_type === 'youtube') {
            $video_final_url = $video_url;
        } elseif ($video_type === 'upload' && isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            // Delete old video if it exists
            if ($lesson['video_type'] === 'upload' && $lesson['video_url']) {
                $old_video_path = ka_upload_path() . 'videos/' . $lesson['video_url'];
                if (file_exists($old_video_path)) {
                    unlink($old_video_path);
                }
            }

            $upload_result = ka_upload_file(
                $_FILES['video_file'],
                ka_upload_path() . 'videos/',
                ['mp4', 'webm', 'ogg', 'm4v', 'mov'],
                config('academy.max_video_mb') * 1048576
            );

            if ($upload_result['success']) {
                $video_final_url = $upload_result['filename'];
            } else {
                return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 
                         'Video upload failed: ' . implode(', ', $upload_result['errors']), 
                         'danger');
            }
        }

        // "Upload" picked without a new file: keep the current video and its type
        if ($video_type === 'upload' && $video_final_url === $lesson['video_url'] && $lesson['video_type'] !== 'upload') {
            $video_type = $lesson['video_type'];
        }

        // Handle attachments
        $attachments = $lesson['attachments'] ? json_decode($lesson['attachments'], true) : [];

        // Add new attachments
        if (isset($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $upload_result = ka_upload_file(
                        $file,
                        ka_upload_path() . 'attachments/',
                        ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'],
                        10485760 // 10MB
                    );

                    if ($upload_result['success']) {
                        $attachments[] = $upload_result['filename'];
                    }
                }
            }
        }

        // Update lesson
        $stmt = $pdo->prepare("
            UPDATE lessons 
            SET title = ?, content = ?, video_url = ?, video_type = ?, duration = ?, attachments = ?
            WHERE id = ? AND course_id = ?
        ");

        $attachments_json = !empty($attachments) ? json_encode(array_values($attachments)) : null;

        if ($stmt->execute([$title, $content, $video_final_url, $video_type, $duration, $attachments_json, $lesson_id, $course_id])) {
            // Log activity
            ka_log_activity($pdo, $user_id, 'updated_lesson', [
                'lesson_id' => $lesson_id,
                'course_id' => $course_id,
                'title' => $title
            ]);

            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Lesson updated successfully', 'success');
        } else {
            return ka_redirect('/admin/manage_lessons.php?course_id=' . $course_id, 'Failed to update lesson', 'danger');
        }

        return response('');
    }

    /** academy/admin/get_lesson (was admin/get_lesson.php) */
    public function getLesson(Request $request)
    {
        extract(Academy::pageVars(true, 'get_lesson'));

        if (!isset($_GET['id'])) {
            return kc_json(['error' => 'Lesson ID required']);
        }

        $lesson_id = (int)$_GET['id'];

        $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$lesson_id]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lesson) {
            // Decode attachments if they exist
            if ($lesson['attachments']) {
                $lesson['attachments'] = json_decode($lesson['attachments'], true);
            }
            return kc_json($lesson);
        } else {
            return kc_json(['error' => 'Lesson not found']);
        }

        return response('');
    }

    /** academy/admin/reorder_lessons (was admin/reorder_lessons.php) */
    public function reorderLessons(Request $request)
    {
        extract(Academy::pageVars(true, 'reorder_lessons'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'error' => 'Invalid method']);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['items']) || !isset($data['course_id']) || !ka_verify_csrf($data['csrf_token'] ?? '')) {
            return kc_json(['success' => false, 'error' => 'Invalid parameters']);
        }

        $items = $data['items'];
        $course_id = (int)$data['course_id'];

        try {
            $pdo->beginTransaction();

            foreach ($items as $index => $lesson_id) {
                $order_number = $index + 1;
                $stmt = $pdo->prepare("UPDATE lessons SET order_number = ? WHERE id = ? AND course_id = ?");
                $stmt->execute([$order_number, $lesson_id, $course_id]);
            }

            $pdo->commit();

            // Log activity
            ka_log_activity($pdo, $user_id, 'reordered_lessons', [
                'course_id' => $course_id,
                'new_order' => $items
            ]);

            return kc_json(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            return kc_json(['success' => false, 'error' => $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/manage_quizzes (was admin/manage_quizzes.php) */
    public function manageQuizzes(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_quizzes'));

        $page_title = 'Manage Quizzes';
        $is_admin_area = true;

        // Get filter parameters
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        $search = $_GET['search'] ?? '';

        // Get all courses for filter
        $stmt = $pdo->query("SELECT id, title FROM courses ORDER BY title");
        $courses = $stmt->fetchAll();

        // Build query
        $query = "
            SELECT q.*, 
                   c.title as course_title,
                   COUNT(DISTINCT qq.id) as question_count,
                   COUNT(DISTINCT qr.id) as attempt_count,
                   AVG(qr.percentage) as avg_score
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            LEFT JOIN quiz_questions qq ON q.id = qq.quiz_id
            LEFT JOIN quiz_results qr ON q.id = qr.quiz_id
            WHERE 1=1
        ";
        $params = [];

        if ($course_id > 0) {
            $query .= " AND q.course_id = ?";
            $params[] = $course_id;
        }

        if (!empty($search)) {
            $query .= " AND (q.title LIKE ? OR c.title LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " GROUP BY q.id ORDER BY q.created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $quizzes = $stmt->fetchAll();

        // Get statistics
        $stats = [
            'total_quizzes' => count($quizzes),
            'total_questions' => 0,
            'total_attempts' => 0,
            'avg_score' => 0
        ];

        foreach ($quizzes as $quiz) {
            $stats['total_questions'] += $quiz['question_count'];
            $stats['total_attempts'] += $quiz['attempt_count'];
        }

        if ($stats['total_attempts'] > 0) {
            $stats['avg_score'] = round(array_sum(array_column($quizzes, 'avg_score')) / count($quizzes));
        }

        $csrf_token = csrf_token();

        return view('academy.admin.manage_quizzes', get_defined_vars());
    }

    /** academy/admin/manage_quiz (was admin/manage_quiz.php) */
    public function manageQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_quiz'));

        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/admin/manage_courses.php', 'Course not found', 'danger');
        }

        // Get or create quiz for this course
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            // Create default quiz
            $stmt = $pdo->prepare("INSERT INTO quizzes (course_id, title, passing_score, attempts_allowed) VALUES (?, ?, 70, 3)");
            $stmt->execute([$course_id, $course['title'] . ' Quiz']);
            $quiz_id = $pdo->lastInsertId();

            // Fetch the new quiz
            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
            $stmt->execute([$quiz_id]);
            $quiz = $stmt->fetch();
        } else {
            $quiz_id = $quiz['id'];
        }

        // Get quiz questions
        $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY order_number");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                if (isset($_POST['action'])) {
                    // Update quiz settings
                    if ($_POST['action'] === 'update_settings') {
                        $title = ka_sanitize($_POST['title']);
                        $description = ka_sanitize($_POST['description']);
                        $passing_score = (int)$_POST['passing_score'];
                        $time_limit = (int)$_POST['time_limit'] ?: null;
                        $attempts_allowed = (int)$_POST['attempts_allowed'];

                        $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, description = ?, passing_score = ?, time_limit = ?, attempts_allowed = ? WHERE id = ?");
                        if ($stmt->execute([$title, $description, $passing_score, $time_limit, $attempts_allowed, $quiz_id])) {
                            $success = 'Quiz settings updated successfully';
                        }
                    }

                    // Add question
                    if ($_POST['action'] === 'add_question') {
                        $question = ka_sanitize($_POST['question']);
                        $option_a = ka_sanitize($_POST['option_a']);
                        $option_b = ka_sanitize($_POST['option_b']);
                        $option_c = ka_sanitize($_POST['option_c']);
                        $option_d = ka_sanitize($_POST['option_d']);
                        $correct_answer = ka_sanitize($_POST['correct_answer']);
                        $points = (int)$_POST['points'];

                        // Get next order number
                        $stmt = $pdo->prepare("SELECT MAX(order_number) FROM quiz_questions WHERE quiz_id = ?");
                        $stmt->execute([$quiz_id]);
                        $order = ($stmt->fetchColumn() ?: 0) + 1;

                        $stmt = $pdo->prepare("
                            INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, points, order_number) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");

                        if ($stmt->execute([$quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points, $order])) {
                            $success = 'Question added successfully';
                            // Refresh questions
                            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY order_number");
                            $stmt->execute([$quiz_id]);
                            $questions = $stmt->fetchAll();
                        }
                    }

                    // Delete question
                    if ($_POST['action'] === 'delete_question' && isset($_POST['question_id'])) {
                        $question_id = (int)$_POST['question_id'];
                        $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?");
                        if ($stmt->execute([$question_id, $quiz_id])) {
                            $success = 'Question deleted successfully';
                            // Refresh questions
                            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY order_number");
                            $stmt->execute([$quiz_id]);
                            $questions = $stmt->fetchAll();
                        }
                    }
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.admin.manage_quiz', get_defined_vars());
    }

    /** academy/admin/edit_quiz (was admin/edit_quiz.php) */
    public function editQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'edit_quiz'));

        $page_title = 'Edit Quiz';
        $is_admin_area = true;

        $quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$quiz_id) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid quiz ID', 'danger');
        }

        // Get quiz details
        $stmt = $pdo->prepare("
            SELECT q.*, c.title as course_title, c.id as course_id
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.id = ?
        ");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            return ka_redirect('/admin/manage_quizzes.php', 'Quiz not found', 'danger');
        }

        // Check if advanced columns exist and set defaults if they don't
        $columns = $pdo->query("SHOW COLUMNS FROM quizzes")->fetchAll(PDO::FETCH_COLUMN);
        $has_shuffle = in_array('shuffle_questions', $columns);
        $has_show_answers = in_array('show_answers', $columns);
        $has_status = in_array('status', $columns);

        // Get all courses for dropdown
        $stmt = $pdo->query("SELECT id, title FROM courses ORDER BY title");
        $courses = $stmt->fetchAll();

        // Get question statistics
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_questions,
                SUM(points) as total_points,
                AVG(points) as avg_points
            FROM quiz_questions 
            WHERE quiz_id = ?
        ");
        $stmt->execute([$quiz_id]);
        $question_stats = $stmt->fetch();

        // Get attempt statistics
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                COUNT(DISTINCT user_id) as unique_users,
                AVG(percentage) as avg_score,
                MAX(percentage) as highest_score,
                COUNT(CASE WHEN passed = 1 THEN 1 END) as passed_count
            FROM quiz_results 
            WHERE quiz_id = ?
        ");
        $stmt->execute([$quiz_id]);
        $attempt_stats = $stmt->fetch();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $title = ka_sanitize($_POST['title'] ?? '');
                $description = ka_sanitize($_POST['description'] ?? '');
                $course_id = (int)($_POST['course_id'] ?? 0);
                $passing_score = (int)($_POST['passing_score'] ?? 70);
                $time_limit = !empty($_POST['time_limit']) ? (int)$_POST['time_limit'] : null;
                $attempts_allowed = (int)($_POST['attempts_allowed'] ?? 3);

                // Only process advanced settings if columns exist
                $shuffle_questions = 0;
                $show_answers = 1;
                $status = 'active';

                if ($has_shuffle) {
                    $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
                }

                if ($has_show_answers) {
                    $show_answers = isset($_POST['show_answers']) ? 1 : 0;
                }

                if ($has_status) {
                    $status = ka_sanitize($_POST['status'] ?? 'active');
                }

                if (empty($title)) {
                    $error = 'Quiz title is required';
                } elseif ($course_id == 0) {
                    $error = 'Please select a course';
                } elseif ($passing_score < 0 || $passing_score > 100) {
                    $error = 'Passing score must be between 0 and 100';
                } else {
                    // Build dynamic update query based on existing columns
                    $update_fields = ["title = ?", "description = ?", "course_id = ?", 
                                     "passing_score = ?", "time_limit = ?", "attempts_allowed = ?"];
                    $params = [$title, $description, $course_id, $passing_score, $time_limit, $attempts_allowed];

                    if ($has_shuffle) {
                        $update_fields[] = "shuffle_questions = ?";
                        $params[] = $shuffle_questions;
                    }

                    if ($has_show_answers) {
                        $update_fields[] = "show_answers = ?";
                        $params[] = $show_answers;
                    }

                    if ($has_status) {
                        $update_fields[] = "status = ?";
                        $params[] = $status;
                    }

                    $params[] = $quiz_id; // Add quiz_id for WHERE clause
                    $update_query = "UPDATE quizzes SET " . implode(", ", $update_fields) . " WHERE id = ?";

                    $stmt = $pdo->prepare($update_query);

                    if ($stmt->execute($params)) {
                        ka_log_activity($pdo, $user_id, 'updated_quiz', ['quiz_id' => $quiz_id]);
                        $success = 'Quiz updated successfully';

                        // Refresh quiz data
                        $stmt = $pdo->prepare("
                            SELECT q.*, c.title as course_title, c.id as course_id
                            FROM quizzes q
                            JOIN courses c ON q.course_id = c.id
                            WHERE q.id = ?
                        ");
                        $stmt->execute([$quiz_id]);
                        $quiz = $stmt->fetch();
                    } else {
                        $error = 'Failed to update quiz';
                    }
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.admin.edit_quiz', get_defined_vars());
    }

    /** academy/admin/delete_quiz (was admin/delete_quiz.php) */
    public function deleteQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'delete_quiz'));

        // Verify CSRF token
        if (!isset($_GET['token']) || !ka_verify_csrf($_GET['token'])) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid security token', 'danger');
        }

        $quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$quiz_id) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid quiz ID', 'danger');
        }

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Delete quiz results first (foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM quiz_results WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);

            // Delete quiz questions
            $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);

            // Delete the quiz
            $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
            $stmt->execute([$quiz_id]);

            // Commit transaction
            $pdo->commit();

            // Log activity
            ka_log_activity($pdo, $user_id, 'deleted_quiz', ['quiz_id' => $quiz_id]);

            return ka_redirect('/admin/manage_quizzes.php', 'Quiz deleted successfully', 'success');

        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            return ka_redirect('/admin/manage_quizzes.php', 'Failed to delete quiz: ' . $e->getMessage(), 'danger');
        }

        return response('');
    }

    /** academy/admin/duplicate_quiz (was admin/duplicate_quiz.php) */
    public function duplicateQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'duplicate_quiz'));

        if (!isset($_GET['token']) || !ka_verify_csrf($_GET['token'])) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid security token', 'danger');
        }

        $quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$quiz_id) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid quiz ID', 'danger');
        }

        try {
            $pdo->beginTransaction();

            // Get original quiz
            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
            $stmt->execute([$quiz_id]);
            $quiz = $stmt->fetch();

            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            // Create new quiz
            $stmt = $pdo->prepare("
                INSERT INTO quizzes (course_id, title, description, passing_score, time_limit, attempts_allowed, 
                                    shuffle_questions, show_answers, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $new_title = $quiz['title'] . ' (Copy)';
            $stmt->execute([
                $quiz['course_id'],
                $new_title,
                $quiz['description'],
                $quiz['passing_score'],
                $quiz['time_limit'],
                $quiz['attempts_allowed'],
                $quiz['shuffle_questions'],
                $quiz['show_answers'],
                'inactive' // Set new quiz as inactive by default
            ]);

            $new_quiz_id = $pdo->lastInsertId();

            // Copy questions
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY order_number");
            $stmt->execute([$quiz_id]);
            $questions = $stmt->fetchAll();

            foreach ($questions as $question) {
                $stmt = $pdo->prepare("
                    INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, 
                                               correct_answer, points, explanation, order_number)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $new_quiz_id,
                    $question['question'],
                    $question['option_a'],
                    $question['option_b'],
                    $question['option_c'],
                    $question['option_d'],
                    $question['correct_answer'],
                    $question['points'],
                    $question['explanation'],
                    $question['order_number']
                ]);
            }

            $pdo->commit();

            ka_log_activity($pdo, $user_id, 'duplicated_quiz', [
                'original_id' => $quiz_id,
                'new_id' => $new_quiz_id
            ]);

            return ka_redirect("/admin/edit_quiz.php?id=$new_quiz_id", 'Quiz duplicated successfully', 'success');

        } catch (Exception $e) {
            $pdo->rollBack();
            return ka_redirect('/admin/manage_quizzes.php', 'Failed to duplicate quiz: ' . $e->getMessage(), 'danger');
        }

        return response('');
    }

    /** academy/admin/preview_quiz (was admin/preview_quiz.php) */
    public function previewQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'preview_quiz'));

        $quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$quiz_id) {
            return response('Invalid quiz ID');
        }

        // Get quiz details
        $stmt = $pdo->prepare("
            SELECT q.*, c.title as course_title 
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.id = ?
        ");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            return response('Quiz not found');
        }

        // Get questions
        $stmt = $pdo->prepare("
            SELECT * FROM quiz_questions 
            WHERE quiz_id = ? 
            ORDER BY order_number
        ");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();

        return view('academy.admin.preview_quiz', get_defined_vars());
    }

    /** academy/admin/reset_quiz_stats (was admin/reset_quiz_stats.php) */
    public function resetQuizStats(Request $request)
    {
        extract(Academy::pageVars(true, 'reset_quiz_stats'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $quiz_id = $data['quiz_id'] ?? 0;
        $token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        try {
            // Delete all quiz results
            $stmt = $pdo->prepare("DELETE FROM quiz_results WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);

            ka_log_activity($pdo, $user_id, 'reset_quiz_stats', ['quiz_id' => $quiz_id]);

            return kc_json(['success' => true]);
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/manage_quiz_questions (was admin/manage_quiz_questions.php) */
    public function manageQuizQuestions(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_quiz_questions'));

        $page_title = 'Manage Quiz Questions';
        $is_admin_area = true;

        $quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

        if (!$quiz_id) {
            return ka_redirect('/admin/manage_quizzes.php', 'Invalid quiz ID', 'danger');
        }

        // --- NEW: AJAX Handler for fetching question data (Prevents need for get_question.php) ---
        if (isset($_GET['ajax_get_question'])) {

            $qid = (int)$_GET['ajax_get_question'];
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ? AND quiz_id = ?");
            $stmt->execute([$qid, $quiz_id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            return kc_json(['success' => !!$question, 'question' => $question]);
        }

        // Get quiz details
        $stmt = $pdo->prepare("
            SELECT q.*, c.title as course_title 
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.id = ?
        ");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            return ka_redirect('/admin/manage_quizzes.php', 'Quiz not found', 'danger');
        }

        // Handle question addition/edition
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if it's a JSON request for duplication
            $json_data = json_decode(file_get_contents('php://input'), true);

            if ($json_data && isset($json_data['action']) && $json_data['action'] === 'duplicate') {

                if (!ka_verify_csrf($json_data['csrf_token'] ?? '')) {
                    return kc_json(['success' => false, 'message' => 'Invalid token']);
                }
                // Duplication Logic
                $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, points, explanation, order_number) 
                                       SELECT quiz_id, CONCAT(question, ' (Copy)'), option_a, option_b, option_c, option_d, correct_answer, points, explanation, order_number + 1 
                                       FROM quiz_questions WHERE id = ?");
                $success = $stmt->execute([$json_data['question_id']]);
                return kc_json(['success' => $success]);
            }

            // Standard Form Submission (Add/Edit)
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $action = $_POST['action'] ?? '';

                if ($action === 'add_question' || $action === 'edit_question') {
                    $question = ka_sanitize($_POST['question'] ?? '');
                    $option_a = ka_sanitize($_POST['option_a'] ?? '');
                    $option_b = ka_sanitize($_POST['option_b'] ?? '');
                    $option_c = ka_sanitize($_POST['option_c'] ?? '');
                    $option_d = ka_sanitize($_POST['option_d'] ?? '');
                    $correct_answer = ka_sanitize($_POST['correct_answer'] ?? 'A');
                    $points = (int)($_POST['points'] ?? 1);
                    $explanation = ka_sanitize($_POST['explanation'] ?? '');

                    if (empty($question) || empty($option_a) || empty($option_b)) {
                        $error = 'Please fill in all required fields';
                    } else {
                        if ($action === 'add_question') {
                            $stmt = $pdo->prepare("SELECT MAX(order_number) FROM quiz_questions WHERE quiz_id = ?");
                            $stmt->execute([$quiz_id]);
                            $order = ($stmt->fetchColumn() ?: 0) + 1;

                            $stmt = $pdo->prepare("
                                INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, points, explanation, order_number) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ");
                            $res = $stmt->execute([$quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points, $explanation, $order]);
                        } else {
                            $question_id = (int)($_POST['question_id'] ?? 0);
                            $stmt = $pdo->prepare("
                                UPDATE quiz_questions 
                                SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, 
                                    correct_answer = ?, points = ?, explanation = ?
                                WHERE id = ? AND quiz_id = ?
                            ");
                            $res = $stmt->execute([$question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points, $explanation, $question_id, $quiz_id]);
                        }

                        if ($res) {
                            ka_flash('Success!', 'success');
                            return ka_redirect("manage_quiz_questions.php?quiz_id=$quiz_id");
                        } else {
                            $error = 'Database error occurred.';
                        }
                    }
                }
            }
        }

        // Handle deletion
        if (isset($_GET['delete'])) {
            if (ka_verify_csrf($_GET['token'] ?? '')) {
                $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE id = ? AND quiz_id = ?");
                $stmt->execute([(int)$_GET['delete'], $quiz_id]);
                return ka_redirect("manage_quiz_questions.php?quiz_id=$quiz_id");
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY order_number");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();
        $csrf_token = csrf_token();

        return view('academy.admin.manage_quiz_questions', get_defined_vars());
    }

    /** academy/admin/get_question (was admin/get_question.php) */
    public function getQuestion(Request $request)
    {
        extract(Academy::pageVars(true, 'get_question'));

        $question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$question_id) {
            return kc_json(['success' => false, 'message' => 'Invalid question ID']);
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $question = $stmt->fetch();

            if ($question) {
                return kc_json(['success' => true, 'question' => $question]);
            } else {
                return kc_json(['success' => false, 'message' => 'Question not found']);
            }
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/duplicate_question (was admin/duplicate_question.php) */
    public function duplicateQuestion(Request $request)
    {
        extract(Academy::pageVars(true, 'duplicate_question'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $question_id = $data['question_id'] ?? 0;
        $quiz_id = $data['quiz_id'] ?? 0;
        $token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        try {
            // Get original question
            $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ? AND quiz_id = ?");
            $stmt->execute([$question_id, $quiz_id]);
            $question = $stmt->fetch();

            if (!$question) {
                return kc_json(['success' => false, 'message' => 'Question not found']);
            }

            // Get next order number
            $stmt = $pdo->prepare("SELECT MAX(order_number) FROM quiz_questions WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);
            $order = ($stmt->fetchColumn() ?: 0) + 1;

            // Duplicate question
            $stmt = $pdo->prepare("
                INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, points, explanation, order_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $quiz_id,
                $question['question'] . ' (Copy)',
                $question['option_a'],
                $question['option_b'],
                $question['option_c'],
                $question['option_d'],
                $question['correct_answer'],
                $question['points'],
                $question['explanation'],
                $order
            ]);

            if ($success) {
                $new_id = $pdo->lastInsertId();
                ka_log_activity($pdo, $user_id, 'duplicated_question', [
                    'original_id' => $question_id,
                    'new_id' => $new_id,
                    'quiz_id' => $quiz_id
                ]);

                return kc_json(['success' => true, 'new_id' => $new_id]);
            } else {
                return kc_json(['success' => false, 'message' => 'Failed to duplicate question']);
            }
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/import_questions (was admin/import_questions.php) */
    public function importQuestions(Request $request)
    {
        extract(Academy::pageVars(true, 'import_questions'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $quiz_id = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
        $token = $_POST['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return kc_json(['success' => false, 'message' => 'Please upload a valid CSV file']);
        }

        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle); // Skip header row

        $imported = 0;
        $errors = [];

        try {
            $pdo->beginTransaction();

            // Get current max order
            $stmt = $pdo->prepare("SELECT MAX(order_number) FROM quiz_questions WHERE quiz_id = ?");
            $stmt->execute([$quiz_id]);
            $order = ($stmt->fetchColumn() ?: 0);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 8) continue;

                $question = trim($row[0] ?? '');
                $option_a = trim($row[1] ?? '');
                $option_b = trim($row[2] ?? '');
                $option_c = trim($row[3] ?? '');
                $option_d = trim($row[4] ?? '');
                $correct_answer = strtoupper(trim($row[5] ?? 'A'));
                $points = (int)($row[6] ?? 1);
                $explanation = trim($row[7] ?? '');

                if (empty($question) || empty($option_a) || empty($option_b)) {
                    $errors[] = "Skipped row " . ($imported + 2) . ": Missing required fields";
                    continue;
                }

                if (!in_array($correct_answer, ['A', 'B', 'C', 'D'])) {
                    $correct_answer = 'A';
                }

                $order++;

                $stmt = $pdo->prepare("
                    INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, points, explanation, order_number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $success = $stmt->execute([
                    $quiz_id, $question, $option_a, $option_b, $option_c, $option_d,
                    $correct_answer, $points, $explanation, $order
                ]);

                if ($success) {
                    $imported++;
                }
            }

            $pdo->commit();

            fclose($handle);

            ka_log_activity($pdo, $user_id, 'imported_questions', [
                'quiz_id' => $quiz_id,
                'count' => $imported
            ]);

            return kc_json([
                'success' => true,
                'count' => $imported,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/manage_users (was admin/manage_users.php) */
    public function manageUsers(Request $request)
    {
        extract(Academy::pageVars(true, 'manage_users'));

        $page_title = 'Manage Users';
        $is_admin_area = true;



        // Handle user actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $action = $_POST['action'] ?? '';
                $target_user_id = (int)($_POST['user_id'] ?? 0);

                switch ($action) {
                    case 'approve':
                        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                        if ($stmt->execute([$target_user_id])) {
                            ka_log_activity($pdo, $user_id, 'approved_user', ['user_id' => $target_user_id]);
                            ka_notify($pdo, $target_user_id, 'Account Approved', 'Your account has been approved. You can now login.', 'success');
                            $success = 'User approved successfully';
                        }
                        break;

                    case 'deactivate':
                        $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
                        if ($stmt->execute([$target_user_id])) {
                            ka_log_activity($pdo, $user_id, 'deactivated_user', ['user_id' => $target_user_id]);
                            $success = 'User deactivated successfully';
                        }
                        break;

                    case 'activate':
                        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                        if ($stmt->execute([$target_user_id])) {
                            ka_log_activity($pdo, $user_id, 'activated_user', ['user_id' => $target_user_id]);
                            $success = 'User activated successfully';
                        }
                        break;

                    case 'reset_password':
                        // People sign in with their K-Connect account, so the password is changed there.
                        $error = 'K-Academy uses the K-Connect login: reset the password from K-Connect (Manage Employees).';
                        break;

                    case 'delete':
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
                        if ($stmt->execute([$target_user_id])) {
                            ka_log_activity($pdo, $user_id, 'deleted_user', ['user_id' => $target_user_id]);
                            $success = 'User deleted successfully';
                        }
                        break;
                }
            }
        }

        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';

        // Build query
        $query = "
            SELECT u.*, 
                   COUNT(DISTINCT e.id) as course_count,
                   COUNT(DISTINCT cert.id) as certificate_count,
                   MAX(e.last_accessed) as last_activity
            FROM users u
            LEFT JOIN enrollments e ON u.id = e.user_id
            LEFT JOIN certificates cert ON u.id = cert.user_id
            WHERE u.role = 'user'
        ";

        $params = [];

        if ($status !== 'all') {
            $query .= " AND u.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $query .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.employee_code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " GROUP BY u.id ORDER BY u.created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.admin.manage_users', get_defined_vars());
    }

    /** academy/admin/get-user-details (was admin/get-user-details.php) */
    public function getUserDetails(Request $request)
    {
        Academy::requireAdmin();
        $ka = Academy::db();
        $u = $ka->selectOne('SELECT id, employee_code, full_name, email, role, status, last_login, created_at FROM users WHERE id = ?', [(int) $request->query('id')]);
        if (! $u) {
            return response('<div class="text-danger">User not found.</div>');
        }
        $employee = \Illuminate\Support\Facades\DB::selectOne('SELECT id, department, position, status FROM employees WHERE employee_code = ? OR email = ? LIMIT 1', [$u['employee_code'], $u['email']]);
        $courses = $ka->select('SELECT c.title, e.status, e.progress_percentage, e.enrolled_at, e.last_accessed FROM enrollments e JOIN courses c ON c.id = e.course_id WHERE e.user_id = ? ORDER BY e.enrolled_at DESC', [$u['id']]);
        $certificates = $ka->select('SELECT cert.certificate_number, cert.status, cert.issued_date, c.title FROM certificates cert JOIN courses c ON c.id = cert.course_id WHERE cert.user_id = ? ORDER BY cert.created_at DESC', [$u['id']]);
        $quizzes = $ka->select('SELECT q.title, qr.percentage, qr.passed, qr.attempt_number, qr.created_at FROM quiz_results qr JOIN quizzes q ON q.id = qr.quiz_id WHERE qr.user_id = ? ORDER BY qr.created_at DESC LIMIT 20', [$u['id']]);

        return view('academy.admin.user-details', compact('u', 'employee', 'courses', 'certificates', 'quizzes'));
    }

    /** academy/admin/pending-certificates (was admin/pending-certificates.php) */
    public function pendingCertificates(Request $request)
    {
        extract(Academy::pageVars(true, 'pending-certificates'));

        // Handle certificate deletion
        if (isset($_GET['delete']) && isset($_GET['token']) && ka_verify_csrf($_GET['token'])) {
            $cert_id = (int)$_GET['delete'];

            // Get certificate details for logging
            $stmt = $pdo->prepare("SELECT certificate_number, user_id, course_id FROM certificates WHERE id = ?");
            $stmt->execute([$cert_id]);
            $cert = $stmt->fetch();

            if ($cert) {
                // Delete the certificate
                $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
                if ($stmt->execute([$cert_id])) {
                    // Log activity
                    ka_log_activity($pdo, $user_id, 'deleted_certificate', [
                        'certificate_id' => $cert_id,
                        'certificate_number' => $cert['certificate_number'],
                        'user_id' => $cert['user_id'],
                        'course_id' => $cert['course_id']
                    ]);

                    ka_flash("Certificate deleted successfully.", "success");
                }
            }
            return ka_redirect('pending-certificates.php');
        }

        // Handle bulk actions
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_action'])) {
            if (!ka_verify_csrf($_POST['token'])) {
                return response('Invalid CSRF token');
            }

            $selected_ids = $_POST['selected_ids'] ?? [];
            $action = $_POST['bulk_action'];

            if (!empty($selected_ids)) {
                $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

                if ($action == 'approve_selected') {
                    // Bulk approve
                    $stmt = $pdo->prepare("UPDATE certificates SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id IN ($placeholders)");
                    $stmt->execute(array_merge([$user_id], $selected_ids));

                    // Create notifications for users
                    foreach ($selected_ids as $cert_id) {
                        $stmt = $pdo->prepare("SELECT user_id, course_id, certificate_number FROM certificates WHERE id = ?");
                        $stmt->execute([$cert_id]);
                        $cert = $stmt->fetch();

                        if ($cert) {
                            $notification_message = "Your certificate has been approved!";
                            $notification_link = "../view-certificate.php?course_id={$cert['course_id']}";

                            $stmt = $pdo->prepare("
                                INSERT INTO notifications (user_id, title, message, type, link, created_at) 
                                VALUES (?, 'Certificate Approved', ?, 'success', ?, NOW())
                            ");
                            $stmt->execute([$cert['user_id'], $notification_message, $notification_link]);
                        }
                    }

                    ka_flash("Selected certificates approved successfully.", "success");

                } elseif ($action == 'delete_selected') {
                    // Bulk delete
                    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id IN ($placeholders)");
                    $stmt->execute($selected_ids);

                    ka_flash("Selected certificates deleted successfully.", "success");
                }
            }
            return ka_redirect('pending-certificates.php');
        }

        // Get filter from query string
        $status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Build query based on filters
        $sql = "
            SELECT c.*, 
                   u.full_name as user_name, u.email, u.employee_code,
                   crs.title as course_title,
                   ad.full_name as approved_by_name
            FROM certificates c
            JOIN users u ON c.user_id = u.id
            JOIN courses crs ON c.course_id = crs.id
            LEFT JOIN users ad ON c.approved_by = ad.id
        ";

        $where_conditions = [];
        $params = [];

        if ($status_filter != 'all') {
            $where_conditions[] = "c.status = ?";
            $params[] = $status_filter;
        }

        if (!empty($search)) {
            $where_conditions[] = "(u.full_name LIKE ? OR u.employee_code LIKE ? OR crs.title LIKE ? OR c.certificate_number LIKE ?)";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $certificates = $stmt->fetchAll();

        // Ensure certificates is an array
        if (!is_array($certificates)) {
            $certificates = [];
        }

        // Get statistics
        $stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'pending'");
        $pending_count = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'approved'");
        $approved_count = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'rejected'");
        $rejected_count = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'approved' AND MONTH(created_at) = MONTH(CURRENT_DATE())");
        $approved_this_month = $stmt->fetchColumn() ?: 0;

        // Generate CSRF token
        $csrf_token = csrf_token();

        return view('academy.admin.pending-certificates', get_defined_vars());
    }

    /** academy/admin/approve-certificate (was admin/approve-certificate.php) */
    public function approveCertificate(Request $request)
    {
        extract(Academy::pageVars(true, 'approve-certificate'));

        $certificate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$certificate_id) {
            return ka_redirect('/admin/pending-certificates.php');
        }

        // Get certificate details
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   u.full_name as user_name, u.email, u.employee_code,
                   crs.title as course_title, crs.description as course_description,
                   qr.percentage as quiz_score, qr.passed as quiz_passed
            FROM certificates c
            JOIN users u ON c.user_id = u.id
            JOIN courses crs ON c.course_id = crs.id
            LEFT JOIN quiz_results qr ON c.quiz_result_id = qr.id
            WHERE c.id = ?
        ");
        $stmt->execute([$certificate_id]);
        $certificate = $stmt->fetch();

        if (!$certificate) {
            return ka_redirect('/admin/pending-certificates.php', 'Certificate not found', 'danger');
        }

        // Handle approval/rejection
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'] ?? '';
            $admin_notes = $_POST['admin_notes'] ?? '';
            $rejection_reason = $_POST['rejection_reason'] ?? '';

            if ($action == 'approve') {
                // The learner downloads the PDF from My Certificates (made when downloaded).
                // Update certificate
                $stmt = $pdo->prepare("
                    UPDATE certificates 
                    SET status = 'approved', 
                        approved_by = ?, 
                        approved_at = NOW(), 
                        admin_notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$user_id, $admin_notes, $certificate_id]);

                // Create notification for user
                $notification_message = "Your certificate for '{$certificate['course_title']}' has been approved!";
                $notification_link = "../view-certificate.php?course_id={$certificate['course_id']}";

                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, title, message, type, link, created_at) 
                    VALUES (?, 'Certificate Approved', ?, 'success', ?, NOW())
                ");
                $stmt->execute([$certificate['user_id'], $notification_message, $notification_link]);

                // Log activity
                ka_log_activity($pdo, $user_id, 'approved_certificate', [
                    'certificate_id' => $certificate_id,
                    'user_id' => $certificate['user_id'],
                    'course_id' => $certificate['course_id']
                ]);

                return ka_redirect('/admin/pending-certificates.php', 'Certificate approved successfully', 'success');

            } elseif ($action == 'reject') {
                // Update certificate
                $stmt = $pdo->prepare("
                    UPDATE certificates 
                    SET status = 'rejected', 
                        approved_by = ?, 
                        approved_at = NOW(), 
                        admin_notes = ?,
                        rejection_reason = ?
                    WHERE id = ?
                ");
                $stmt->execute([$user_id, $admin_notes, $rejection_reason, $certificate_id]);

                // Create notification for user
                $notification_message = "Your certificate request for '{$certificate['course_title']}' was rejected.";
                if ($rejection_reason) {
                    $notification_message .= " Reason: " . $rejection_reason;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, title, message, type, link, created_at) 
                    VALUES (?, 'Certificate Rejected', ?, 'danger', NULL, NOW())
                ");
                $stmt->execute([$certificate['user_id'], $notification_message]);

                // Log activity
                ka_log_activity($pdo, $user_id, 'rejected_certificate', [
                    'certificate_id' => $certificate_id,
                    'user_id' => $certificate['user_id'],
                    'course_id' => $certificate['course_id'],
                    'reason' => $rejection_reason
                ]);

                return ka_redirect('/admin/pending-certificates.php', 'Certificate rejected', 'warning');
            }
        }

        return view('academy.admin.approve-certificate', get_defined_vars());
    }

    /** academy/admin/reports (was admin/reports.php) */
    public function reports(Request $request)
    {
        extract(Academy::pageVars(true, 'reports'));

        $page_title = 'Reports';
        $is_admin_area = true;



        // Get date range
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        // Overall statistics
        $stats = [];

        // Total users over time
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM users
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $stmt->execute([$start_date, $end_date]);
        $user_growth = $stmt->fetchAll();

        // Enrollments over time
        $stmt = $pdo->prepare("
            SELECT DATE(enrolled_at) as date, COUNT(*) as count
            FROM enrollments
            WHERE DATE(enrolled_at) BETWEEN ? AND ?
            GROUP BY DATE(enrolled_at)
            ORDER BY date
        ");
        $stmt->execute([$start_date, $end_date]);
        $enrollments_over_time = $stmt->fetchAll();

        // Course completion rates
        $stmt = $pdo->query("
            SELECT c.title, 
                   COUNT(e.id) as total_enrollments,
                   COUNT(CASE WHEN e.status = 'completed' THEN 1 END) as completions
            FROM courses c
            LEFT JOIN enrollments e ON c.id = e.course_id
            GROUP BY c.id
            ORDER BY completions DESC
            LIMIT 10
        ");
        $course_completion = $stmt->fetchAll();

        // User engagement
        $stmt = $pdo->query("
            SELECT 
                COUNT(DISTINCT user_id) as active_users,
                AVG(progress_percentage) as avg_progress,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_courses
            FROM enrollments
            WHERE last_accessed >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $engagement = $stmt->fetch();

        // Quiz performance
        $stmt = $pdo->query("
            SELECT 
                AVG(percentage) as avg_score,
                COUNT(CASE WHEN passed = 1 THEN 1 END) as passed_count,
                COUNT(*) as total_attempts
            FROM quiz_results
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $quiz_stats = $stmt->fetch();

        // Top performing students
        $stmt = $pdo->query("
            SELECT u.full_name, u.employee_code,
                   COUNT(DISTINCT cert.id) as certificates,
                   AVG(qr.percentage) as avg_quiz_score
            FROM users u
            LEFT JOIN certificates cert ON u.id = cert.user_id
            LEFT JOIN quiz_results qr ON u.id = qr.user_id
            WHERE u.role = 'user'
            GROUP BY u.id
            HAVING certificates > 0
            ORDER BY certificates DESC, avg_quiz_score DESC
            LIMIT 10
        ");
        $top_students = $stmt->fetchAll();

        return view('academy.admin.reports', get_defined_vars());
    }

    /** academy/admin/activity-logs (was admin/activity-logs.php) */
    public function activityLogs(Request $request)
    {
        extract(Academy::pageVars(true, 'activity-logs'));

        $page_title = 'Activity Logs';
        $is_admin_area = true;



        // Get filter parameters
        $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-7 days'));
        $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        // Build query
        $query = "
            SELECT al.*, u.full_name, u.employee_code
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE DATE(al.created_at) BETWEEN ? AND ?
        ";
        $params = [$date_from, $date_to];

        if ($user_id > 0) {
            $query .= " AND al.user_id = ?";
            $params[] = $user_id;
        }

        if (!empty($action)) {
            $query .= " AND al.action LIKE ?";
            $params[] = "%$action%";
        }

        $query .= " ORDER BY al.created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        // Get users for filter
        $users = $pdo->query("SELECT id, full_name, employee_code FROM users ORDER BY full_name")->fetchAll();

        // Get unique actions
        $actions = $pdo->query("SELECT DISTINCT action FROM activity_logs ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);

        return view('academy.admin.activity-logs', get_defined_vars());
    }

    /** academy/admin/settings (was admin/settings.php) */
    public function settings(Request $request)
    {
        extract(Academy::pageVars(true, 'settings'));

        $page_title = 'System Settings';
        $is_admin_area = true;



        $error = '';
        $success = '';

        // Handle settings update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $section = $_POST['section'] ?? '';

                if ($section === 'general') {
                    // Update general settings
                    $app_name = ka_sanitize($_POST['app_name'] ?? '');
                    $app_email = ka_sanitize($_POST['app_email'] ?? '');
                    $app_url = ka_sanitize($_POST['app_url'] ?? '');

                    // Save to config file or database
                    // For now, just show success
                    $success = 'General settings updated successfully';
                } elseif ($section === 'email') {
                    // Update email settings
                    $smtp_host = ka_sanitize($_POST['smtp_host'] ?? '');
                    $smtp_port = (int)($_POST['smtp_port'] ?? 587);
                    $smtp_user = ka_sanitize($_POST['smtp_user'] ?? '');
                    $smtp_pass = $_POST['smtp_pass'] ?? '';

                    $success = 'Email settings updated successfully';
                } elseif ($section === 'security') {
                    // Update security settings
                    $session_timeout = (int)($_POST['session_timeout'] ?? 30);
                    $max_login_attempts = (int)($_POST['max_login_attempts'] ?? 5);

                    $success = 'Security settings updated successfully';
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.admin.settings', get_defined_vars());
    }

    /** academy/admin/hero-video (was admin/hero-video.php) */
    public function heroVideo(Request $request)
    {
        extract(Academy::pageVars(true, 'hero-video'));

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $video_url = ka_sanitize($_POST['video_url'] ?? '');
                $video_title = ka_sanitize($_POST['video_title'] ?? '');
                $video_description = ka_sanitize($_POST['video_description'] ?? '');
                $status = isset($_POST['status']) ? 1 : 0;

                // Check if record exists
                $stmt = $pdo->query("SELECT id FROM homepage_hero LIMIT 1");
                $existing = $stmt->fetch();

                if ($existing) {
                    // Update
                    $stmt = $pdo->prepare("UPDATE homepage_hero SET video_url = ?, video_title = ?, video_description = ?, status = ? WHERE id = ?");
                    $stmt->execute([$video_url, $video_title, $video_description, $status, $existing['id']]);
                } else {
                    // Insert
                    $stmt = $pdo->prepare("INSERT INTO homepage_hero (video_url, video_title, video_description, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$video_url, $video_title, $video_description, $status]);
                }

                ka_flash('Hero video updated successfully', 'success');
                return ka_redirect('admin/hero-video.php');
            }
        }

        // Get current hero video
        $stmt = $pdo->query("SELECT * FROM homepage_hero LIMIT 1");
        $hero = $stmt->fetch();

        $csrf_token = csrf_token();

        return view('academy.admin.hero-video', get_defined_vars());
    }

    /** academy/admin/post-comment (was admin/post-comment.php) */
    public function postComment(Request $request)
    {
        extract(Academy::pageVars(true, 'post-comment'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $lesson_id = $data['lesson_id'] ?? 0;
        $comment = trim($data['comment'] ?? '');
        $parent_id = $data['parent_id'] ?? null;
        $token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        if (empty($comment)) {
            return kc_json(['success' => false, 'message' => 'Comment cannot be empty']);
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO discussions (user_id, lesson_id, parent_id, comment, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$user_id, $lesson_id, $parent_id, $comment]);

            $comment_id = $pdo->lastInsertId();

            ka_log_activity($pdo, $user_id, 'posted_comment', [
                'lesson_id' => $lesson_id,
                'comment_id' => $comment_id
            ]);

            // Get the inserted comment with user details
            $stmt = $pdo->prepare("
                SELECT d.*, u.full_name, u.profile_image 
                FROM discussions d
                JOIN users u ON d.user_id = u.id
                WHERE d.id = ?
            ");
            $stmt->execute([$comment_id]);
            $newComment = $stmt->fetch();

            return kc_json([
                'success' => true, 
                'message' => 'Comment posted successfully',
                'comment' => $newComment
            ]);
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/admin/reorder_questions (JSON: quiz_id, items = question ids in order); the old file was empty */
    public function reorderQuestions(Request $request)
    {
        Academy::requireAdmin();
        $ka = Academy::db();
        $quizId = (int) $request->json('quiz_id');
        foreach (array_values((array) $request->json('items', [])) as $i => $id) {
            $ka->update('UPDATE quiz_questions SET order_number = ? WHERE id = ? AND quiz_id = ?', [$i + 1, (int) $id, $quizId]);
        }

        return response()->json(['success' => true]);
    }

    /** academy/admin/add_quiz: pick the course; its quiz is then set up on manage_quiz (one quiz per course) */
    public function addQuiz(Request $request)
    {
        extract(Academy::pageVars(true, 'add_quiz'));
        $page_title = 'Add Quiz';
        if ($request->filled('course_id')) {
            return redirect(url('academy/admin/manage_quiz').'?course_id='.(int) $request->input('course_id'));
        }
        $courses = Academy::db()->select('SELECT c.id, c.title, c.status, q.id AS quiz_id FROM courses c LEFT JOIN quizzes q ON q.course_id = c.id ORDER BY c.title');

        return view('academy.admin.add_quiz', get_defined_vars());
    }

    /** academy/admin/quiz_results?quiz_id= : every attempt at one quiz */
    public function quizResults(Request $request)
    {
        extract(Academy::pageVars(true, 'quiz_results'));
        $ka = Academy::db();
        $quiz = $ka->selectOne('SELECT q.*, c.title AS course_title FROM quizzes q JOIN courses c ON c.id = q.course_id WHERE q.id = ?', [(int) $request->query('quiz_id')]);
        if (! $quiz) {
            return ka_redirect('/admin/manage_quizzes.php', 'Quiz not found', 'danger');
        }
        $page_title = 'Quiz Results';
        $results = $ka->select('SELECT qr.*, u.full_name, u.employee_code FROM quiz_results qr JOIN users u ON u.id = qr.user_id
            WHERE qr.quiz_id = ? ORDER BY qr.created_at DESC', [$quiz['id']]);
        $summary = $ka->selectOne('SELECT COUNT(*) AS attempts, COUNT(DISTINCT user_id) AS people, SUM(passed = 1) AS passed,
            ROUND(AVG(percentage), 1) AS average FROM quiz_results WHERE quiz_id = ?', [$quiz['id']]);

        return view('academy.admin.quiz_results', get_defined_vars());
    }
}
