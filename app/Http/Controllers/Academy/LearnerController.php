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
 * K-Academy learner pages (were kacademy/*.php).
 * The page code is the old K-Academy code on the "kacademy" database connection; it uses
 * $pdo (Academy::pdo()), $user_id/$user (Academy::pageVars()) and the ka_* helpers.
 */
class LearnerController extends Controller
{
    /** academy/index (was index.php) */
    public function index(Request $request)
    {
        extract(Academy::publicVars('index'));

        // Get hero video
        $stmt = $pdo->query("SELECT * FROM homepage_hero WHERE status = 1 LIMIT 1");
        $hero = $stmt->fetch();

        // Extract YouTube ID for the hero video
        $hero_youtube_id = '';
        if ($hero && !empty($hero['video_url'])) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $hero['video_url'], $matches);
            $hero_youtube_id = $matches[1] ?? '';
        }

        // Get courses by category type
        $foundation_courses = $pdo->query("
            SELECT * FROM courses 
            WHERE status = 'published' AND category_type = 'foundation' 
            ORDER BY created_at DESC LIMIT 3
        ")->fetchAll();

        $mastery_courses = $pdo->query("
            SELECT * FROM courses 
            WHERE status = 'published' AND category_type = 'mastery' 
            ORDER BY created_at DESC LIMIT 3
        ")->fetchAll();

        $compliance_courses = $pdo->query("
            SELECT * FROM courses 
            WHERE status = 'published' AND category_type = 'compliance' 
            ORDER BY created_at DESC LIMIT 3
        ")->fetchAll();

        // Get resource count
        $resource_count = $pdo->query("
            SELECT COUNT(*) FROM courses 
            WHERE status = 'published' AND category_type = 'resource'
        ")->fetchColumn();

        // Company stats
        $total_employees = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
        $total_courses = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
        $total_completions = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();

        return view('academy.index', get_defined_vars());
    }

    /** academy/dashboard (was dashboard.php) */
    public function dashboard(Request $request)
    {
        extract(Academy::pageVars(false, 'dashboard'));


        // Get enrolled courses with progress
        $stmt = $pdo->prepare("
            SELECT c.*, e.progress_percentage, e.status as enrollment_status, e.last_accessed, e.enrolled_at,
                   COUNT(DISTINCT l.id) as total_lessons,
                   COUNT(DISTINCT lp.id) as completed_lessons,
                   q.id as quiz_id,
                   qr.passed as quiz_passed
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = e.user_id AND lp.completed = 1
            LEFT JOIN quizzes q ON c.id = q.course_id
            LEFT JOIN quiz_results qr ON q.id = qr.quiz_id AND qr.user_id = e.user_id
            WHERE e.user_id = ?
            GROUP BY c.id, e.id
            ORDER BY e.last_accessed DESC
        ");
        $stmt->execute([$user['id']]);
        $enrollments = $stmt->fetchAll();

        // Get certificates
        $stmt = $pdo->prepare("
            SELECT cert.*, c.title as course_title 
            FROM certificates cert
            JOIN courses c ON cert.course_id = c.id
            WHERE cert.user_id = ?
            ORDER BY cert.issued_date DESC
        ");
        $stmt->execute([$user['id']]);
        $certificates = $stmt->fetchAll();

        // Get recent activity
        $stmt = $pdo->prepare("
            SELECT * FROM activity_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$user['id']]);
        $activities = $stmt->fetchAll();

        // Get notifications
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$user['id']]);
        $notifications = $stmt->fetchAll();

        // Mark notifications as read
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$user['id']]);

        // Calculate overall statistics
        $total_courses = count($enrollments);
        $completed_courses = count(array_filter($enrollments, fn($e) => $e['enrollment_status'] === 'completed'));
        $in_progress_courses = count(array_filter($enrollments, fn($e) => $e['enrollment_status'] === 'in_progress'));
        $total_certificates = count($certificates);

        // Calculate overall progress percentage
        $total_lessons = array_sum(array_column($enrollments, 'total_lessons'));
        $completed_lessons = array_sum(array_column($enrollments, 'completed_lessons'));
        $overall_progress = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

        // Get continue learning course
        $continue_course = null;
        foreach ($enrollments as $course) {
            if ($course['enrollment_status'] === 'in_progress' && $course['progress_percentage'] < 100) {
                $continue_course = $course;
                break;
            }
        }

        // Home page video the admin set (academy/admin/hero-video)
        $hero = $pdo->query("SELECT * FROM homepage_hero WHERE status = 1 LIMIT 1")->fetch();
        $hero_video = $hero ? Academy::video($hero['video_url']) : ['kind' => 'none'];

        return view('academy.dashboard', get_defined_vars());
    }

    /** academy/browse-courses (was browse-courses.php) */
    public function browseCourses(Request $request)
    {
        extract(Academy::pageVars(false, 'browse-courses'));

        $page_title = 'Browse Courses';



        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Build query based on filters
        $sql = "
            SELECT c.*, 
                   COUNT(l.id) as lesson_count,
                   COUNT(DISTINCT e.user_id) as enrolled_count
            FROM courses c
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN enrollments e ON c.id = e.course_id
            WHERE c.status = 'published'
        ";

        $params = [];

        if (!empty($type)) {
            $sql .= " AND c.category_type = ?";
            $params[] = $type;
        }

        if (!empty($category)) {
            $sql .= " AND c.category LIKE ?";
            $params[] = "%$category%";
        }

        if (!empty($search)) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();

        // Get all categories for filter
        $categories = $pdo->query("SELECT DISTINCT category FROM courses WHERE status = 'published' AND category IS NOT NULL ORDER BY category")->fetchAll();

        // Get type counts
        $type_counts = [];
        $type_counts['foundation'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'foundation'")->fetchColumn();
        $type_counts['mastery'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'mastery'")->fetchColumn();
        $type_counts['resource'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'resource'")->fetchColumn();
        $type_counts['compliance'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'compliance'")->fetchColumn();

        $type_labels = [
            'foundation' => ['K-Start: Foundation', '#4299e1'],
            'mastery' => ['K-Expert: Mastery', '#48bb78'],
            'resource' => ['K-Library: Resource', '#ed8936'],
            'compliance' => ['K-Compliance', '#9f7aea']
        ];

        return view('academy.browse-courses', get_defined_vars());
    }

    /** academy/browse-course (was browse-course.php) */
    public function browseCourse(Request $request)
    {
        extract(Academy::pageVars(false, 'browse-course'));

        $page_title = 'Browse Courses';



        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Build query based on filters
        $sql = "
            SELECT c.*, 
                   COUNT(l.id) as lesson_count,
                   COUNT(DISTINCT e.user_id) as enrolled_count
            FROM courses c
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN enrollments e ON c.id = e.course_id
            WHERE c.status = 'published'
        ";

        $params = [];

        if (!empty($type)) {
            $sql .= " AND c.category_type = ?";
            $params[] = $type;
        }

        if (!empty($category)) {
            $sql .= " AND c.category LIKE ?";
            $params[] = "%$category%";
        }

        if (!empty($search)) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();

        // Get all categories for filter
        $categories = $pdo->query("SELECT DISTINCT category FROM courses WHERE status = 'published' AND category IS NOT NULL ORDER BY category")->fetchAll();

        // Get type counts
        $type_counts = [];
        $type_counts['foundation'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'foundation'")->fetchColumn();
        $type_counts['mastery'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'mastery'")->fetchColumn();
        $type_counts['resource'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'resource'")->fetchColumn();
        $type_counts['compliance'] = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'published' AND category_type = 'compliance'")->fetchColumn();

        $type_labels = [
            'foundation' => ['K-Start: Foundation', '#4299e1'],
            'mastery' => ['K-Expert: Mastery', '#48bb78'],
            'resource' => ['K-Library: Resource', '#ed8936'],
            'compliance' => ['K-Compliance', '#9f7aea']
        ];

        return view('academy.browse-course', get_defined_vars());
    }

    /** academy/course-overview (was course-overview.php) */
    public function courseOverview(Request $request)
    {
        extract(Academy::pageVars(false, 'course-overview'));

        $page_title = 'Course Overview';



        $course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$course_id) {
            return ka_redirect('/browse-courses.php', 'Invalid course ID', 'danger');
        }

        // Get course details
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   COUNT(DISTINCT l.id) as total_lessons,
                   COUNT(DISTINCT e.id) as total_enrollments,
                   u.full_name as created_by_name
            FROM courses c
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN enrollments e ON c.id = e.course_id
            LEFT JOIN users u ON c.created_by = u.id
            WHERE c.id = ? AND c.status = 'published'
            GROUP BY c.id
        ");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/browse-courses.php', 'Course not found', 'danger');
        }

        // Check if user is enrolled
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        $enrollment = $stmt->fetch();

        // Get all lessons with progress
        $stmt = $pdo->prepare("
            SELECT l.*, 
                   lp.completed,
                   lp.watched_duration,
                   lp.completed_at
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
            WHERE l.course_id = ?
            ORDER BY l.order_number
        ");
        $stmt->execute([$user_id, $course_id]);
        $lessons = $stmt->fetchAll();

        // Get instructor info
        if ($course['created_by']) {
            $stmt = $pdo->prepare("SELECT full_name, employee_code, profile_image, email FROM users WHERE id = ?");
            $stmt->execute([$course['created_by']]);
            $instructor = $stmt->fetch();
        } else {
            $instructor = null;
        }

        $csrf_token = csrf_token();

        return view('academy.course-overview', get_defined_vars());
    }

    /** academy/course-player (was course-player.php) */
    public function coursePlayer(Request $request)
    {
        extract(Academy::pageVars(false, 'course-player'));

        $page_title = 'Course Player';



        $course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;

        // Get course details and verify enrollment
        $stmt = $pdo->prepare("
            SELECT c.*, e.progress_percentage, e.status as enrollment_status
            FROM courses c
            JOIN enrollments e ON c.id = e.course_id
            WHERE c.id = ? AND e.user_id = ?
        ");
        $stmt->execute([$course_id, $user_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/browse-courses.php', 'You are not enrolled in this course', 'warning');
        }

        // Get all lessons for this course
        $stmt = $pdo->prepare("
            SELECT l.*, 
                   lp.completed,
                   lp.watched_duration,
                   lp.completed_at,
                   lp.id as progress_id
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
            WHERE l.course_id = ?
            ORDER BY l.order_number
        ");
        $stmt->execute([$user_id, $course_id]);
        $lessons = $stmt->fetchAll();

        // Calculate total lessons and completed lessons for progress
        $total_lessons = count($lessons);
        $completed_lessons = count(array_filter($lessons, fn($l) => $l['completed']));
        $progress_percentage = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

        // If no lesson specified, get first incomplete lesson or first lesson
        if (!$lesson_id) {
            foreach ($lessons as $lesson) {
                if (!$lesson['completed']) {
                    $lesson_id = $lesson['id'];
                    break;
                }
            }
            if (!$lesson_id && !empty($lessons)) {
                $lesson_id = $lessons[0]['id'];
            }
        }

        // Get current lesson details
        $current_lesson = null;
        foreach ($lessons as $lesson) {
            if ($lesson['id'] == $lesson_id) {
                $current_lesson = $lesson;
                break;
            }
        }

        if (!$current_lesson) {
            return ka_redirect('/my-courses.php', 'Lesson not found', 'danger');
        }

        // YouTube, uploaded file, or an outside link (Vimeo, Google Drive, .mp4 address...)
        $video = Academy::video($current_lesson['video_url'], $current_lesson['video_type']);

        // Update last accessed
        $stmt = $pdo->prepare("UPDATE enrollments SET last_accessed = NOW() WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);

        // Calculate next and previous lessons
        $current_index = array_search($current_lesson, $lessons);
        $prev_lesson = $current_index > 0 ? $lessons[$current_index - 1] : null;
        $next_lesson = $current_index < count($lessons) - 1 ? $lessons[$current_index + 1] : null;

        // Get quiz for this course
        $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $quiz = $stmt->fetch();

        // Check if quiz is passed
        $quiz_passed = false;
        $quiz_attempts = 0;
        $best_score = 0;
        if ($quiz) {
            $stmt = $pdo->prepare("SELECT passed, percentage FROM quiz_results WHERE user_id = ? AND quiz_id = ? ORDER BY percentage DESC LIMIT 1");
            $stmt->execute([$user_id, $quiz['id']]);
            $result = $stmt->fetch();
            if ($result) {
                $quiz_passed = $result['passed'];
                $best_score = $result['percentage'];
            }

            // Count attempts
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ? AND quiz_id = ?");
            $stmt->execute([$user_id, $quiz['id']]);
            $quiz_attempts = $stmt->fetchColumn();
        }

        // Check if course is completed
        $course_completed = ($completed_lessons == $total_lessons && $total_lessons > 0);

        // Get certificate if exists
        $certificate = null;
        if ($course_completed && $quiz_passed) {
            $stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$user_id, $course_id]);
            $certificate = $stmt->fetch();
        }

        // Get counts for tabs
        // Resources count
        $stmt = $pdo->prepare("SELECT attachments FROM lessons WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $all_lessons_attachments = $stmt->fetchAll();
        $resources_count = 0;
        foreach ($all_lessons_attachments as $lesson) {
            if ($lesson['attachments']) {
                $attachments = json_decode($lesson['attachments'], true);
                $resources_count += count($attachments);
            }
        }

        // Discussions count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM discussions d 
            JOIN lessons l ON d.lesson_id = l.id 
            WHERE l.course_id = ?
        ");
        $stmt->execute([$course_id]);
        $discussions_count = $stmt->fetchColumn();

        // Notes count (users who have taken notes for this course)
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT ln.id) FROM lesson_notes ln
            JOIN lessons l ON ln.lesson_id = l.id
            WHERE l.course_id = ? AND ln.user_id = ?
        ");
        $stmt->execute([$course_id, $user_id]);
        $notes_count = $stmt->fetchColumn();

        $csrf_token = csrf_token();

        return view('academy.course-player', get_defined_vars());
    }

    /** academy/course-notes (was course-notes.php) */
    public function courseNotes(Request $request)
    {
        extract(Academy::pageVars(false, 'course-notes'));

        $page_title = 'My Course Notes';



        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/my-courses.php', 'Course not found', 'danger');
        }

        // Get all lessons with notes for this user
        $stmt = $pdo->prepare("
            SELECT l.id, l.title, l.order_number, ln.notes, ln.updated_at
            FROM lessons l
            LEFT JOIN lesson_notes ln ON l.id = ln.lesson_id AND ln.user_id = ?
            WHERE l.course_id = ?
            ORDER BY l.order_number
        ");
        $stmt->execute([$user_id, $course_id]);
        $lessons = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.course-notes', get_defined_vars());
    }

    /** academy/course-discussions (was course-discussions.php) */
    public function courseDiscussions(Request $request)
    {
        extract(Academy::pageVars(false, 'course-discussions'));

        $page_title = 'Course Discussions';



        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/my-courses.php', 'Course not found', 'danger');
        }

        // Get all discussions for this course
        $stmt = $pdo->prepare("
            SELECT d.*, 
                   u.full_name, u.profile_image, u.employee_code,
                   l.title as lesson_title, l.order_number as lesson_order
            FROM discussions d
            JOIN users u ON d.user_id = u.id
            JOIN lessons l ON d.lesson_id = l.id
            WHERE l.course_id = ? AND d.parent_id IS NULL
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$course_id]);
        $discussions = $stmt->fetchAll();

        // Get replies for each discussion
        foreach ($discussions as &$discussion) {
            $stmt = $pdo->prepare("
                SELECT d.*, u.full_name, u.profile_image, u.employee_code
                FROM discussions d
                JOIN users u ON d.user_id = u.id
                WHERE d.parent_id = ?
                ORDER BY d.created_at ASC
            ");
            $stmt->execute([$discussion['id']]);
            $discussion['replies'] = $stmt->fetchAll();
        }
        unset($discussion);

        $csrf_token = csrf_token();

        return view('academy.course-discussions', get_defined_vars());
    }

    /** academy/course-resources (was course-resources.php) */
    public function courseResources(Request $request)
    {
        extract(Academy::pageVars(false, 'course-resources'));

        $page_title = 'Course Resources';



        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        // Get course details
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            return ka_redirect('/my-courses.php', 'Course not found', 'danger');
        }

        // Get all resources grouped by lesson
        $stmt = $pdo->prepare("
            SELECT l.id as lesson_id, l.title as lesson_title, l.order_number,
                   l.attachments
            FROM lessons l
            WHERE l.course_id = ? AND l.attachments IS NOT NULL
            ORDER BY l.order_number
        ");
        $stmt->execute([$course_id]);
        $lessons_with_resources = $stmt->fetchAll();

        // Collect all resources
        $all_resources = [];
        $total_size = 0;
        foreach ($lessons_with_resources as $lesson) {
            $attachments = json_decode($lesson['attachments'], true);
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $file_path = 'assets/uploads/attachments/' . $file;
                    $file_size = file_exists($file_path) ? filesize($file_path) : 0;
                    $total_size += $file_size;

                    $all_resources[] = [
                        'lesson_id' => $lesson['lesson_id'],
                        'lesson_title' => $lesson['lesson_title'],
                        'lesson_order' => $lesson['order_number'],
                        'file_name' => $file,
                        'file_path' => $file_path,
                        'file_size' => $file_size,
                        'file_ext' => strtolower(pathinfo($file, PATHINFO_EXTENSION))
                    ];
                }
            }
        }




        $csrf_token = csrf_token();

        return view('academy.course-resources', get_defined_vars());
    }

    /** academy/my-courses (was my-courses.php) */
    public function myCourses(Request $request)
    {
        extract(Academy::pageVars(false, 'my-courses'));

        $page_title = 'My Courses';
        $show_search = true;




        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';

        // Build query
        $query = "
            SELECT c.*, e.progress_percentage, e.status as enrollment_status, e.enrolled_at, e.last_accessed,
                   COUNT(DISTINCT l.id) as total_lessons,
                   COUNT(DISTINCT lp.id) as completed_lessons,
                   q.id as quiz_id,
                   qr.passed as quiz_passed,
                   qr.percentage as quiz_score,
                   cert.id as certificate_id,
                   cert.certificate_number,
                   cert.issued_date
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = e.user_id AND lp.completed = 1
            LEFT JOIN quizzes q ON c.id = q.course_id
            LEFT JOIN quiz_results qr ON q.id = qr.quiz_id AND qr.user_id = e.user_id
            LEFT JOIN certificates cert ON c.id = cert.course_id AND cert.user_id = e.user_id
            WHERE e.user_id = ?
        ";

        $params = [$user_id];

        if ($status !== 'all') {
            $query .= " AND e.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $query .= " AND c.title LIKE ?";
            $params[] = "%$search%";
        }

        $query .= " GROUP BY c.id ORDER BY 
            CASE 
                WHEN e.status = 'in_progress' THEN 1
                WHEN e.status = 'completed' THEN 2
                ELSE 3
            END, e.last_accessed DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $enrollments = $stmt->fetchAll();

        // Get statistics
        $stats = [
            'total' => count($enrollments),
            'completed' => count(array_filter($enrollments, fn($e) => $e['enrollment_status'] === 'completed')),
            'in_progress' => count(array_filter($enrollments, fn($e) => $e['enrollment_status'] === 'in_progress')),
            'certificates' => count(array_filter($enrollments, fn($e) => !empty($e['certificate_id']))),
            'total_lessons_completed' => array_sum(array_column($enrollments, 'completed_lessons')),
            'total_lessons' => array_sum(array_column($enrollments, 'total_lessons'))
        ];

        // Calculate overall progress
        $overall_progress = $stats['total_lessons'] > 0 
            ? round(($stats['total_lessons_completed'] / $stats['total_lessons']) * 100) 
            : 0;

        // Get recent activity
        $stmt = $pdo->prepare("
            SELECT * FROM lesson_progress 
            WHERE user_id = ? 
            ORDER BY last_accessed DESC 
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recent_activity = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.my-courses', get_defined_vars());
    }

    /** academy/progress (was progress.php) */
    public function progress(Request $request)
    {
        extract(Academy::pageVars(false, 'progress'));

        $page_title = 'My Learning Progress';



        $period = isset($_GET['period']) ? $_GET['period'] : 'all';

        // Get user details
        $stmt = $pdo->prepare("SELECT full_name, employee_code, profile_image FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // Get overall statistics
        $stats = [];

        // Total courses and progress
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_courses,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_courses,
                COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_courses,
                ROUND(AVG(progress_percentage), 1) as avg_progress
            FROM enrollments 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $stats = $stmt->fetch();

        // Total lessons and completed
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT l.id) as total_lessons,
                COUNT(DISTINCT lp.id) as completed_lessons
            FROM lessons l
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ? AND lp.completed = 1
            WHERE l.course_id IN (SELECT course_id FROM enrollments WHERE user_id = ?)
        ");
        $stmt->execute([$user_id, $user_id]);
        $lesson_stats = $stmt->fetch();

        // Quiz statistics
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_quizzes,
                COUNT(CASE WHEN passed = 1 THEN 1 END) as passed_quizzes,
                ROUND(AVG(percentage), 1) as avg_score,
                ROUND(MAX(percentage), 1) as best_score
            FROM quiz_results 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $quiz_stats = $stmt->fetch();

        // Time spent learning
        $stmt = $pdo->prepare("
            SELECT SUM(watched_duration) as total_minutes
            FROM lesson_progress 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $time_stats = $stmt->fetch();
        $total_hours = floor(($time_stats['total_minutes'] ?? 0) / 60);
        $total_minutes = ($time_stats['total_minutes'] ?? 0) % 60;

        // Certificates earned
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $certificates_count = $stmt->fetchColumn();

        // Get detailed course-wise progress with lesson breakdown
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.title,
                c.thumbnail,
                c.level,
                c.category,
                e.progress_percentage,
                e.status,
                e.enrolled_at,
                e.completed_at,
                e.last_accessed,
                COUNT(DISTINCT l.id) as total_lessons,
                COUNT(DISTINCT lp.id) as completed_lessons,
                qr.passed as quiz_passed,
                qr.percentage as quiz_score,
                cert.id as certificate_id
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = e.user_id AND lp.completed = 1
            LEFT JOIN quiz_results qr ON qr.quiz_id = c.id AND qr.user_id = e.user_id
            LEFT JOIN certificates cert ON cert.course_id = c.id AND cert.user_id = e.user_id
            WHERE e.user_id = ?
            GROUP BY c.id, e.id
            ORDER BY 
                CASE WHEN e.status = 'in_progress' THEN 1 ELSE 2 END,
                e.last_accessed DESC
        ");
        $stmt->execute([$user_id]);
        $course_progress = $stmt->fetchAll();

        // Get recent achievements
        $stmt = $pdo->prepare("
            (SELECT 
                'course_completed' as type,
                c.title as title,
                e.completed_at as achieved_date,
                c.id as reference_id
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.user_id = ? AND e.status = 'completed'
            ORDER BY e.completed_at DESC
            LIMIT 3)
            UNION ALL
            (SELECT 
                'certificate' as type,
                c.title as title,
                cert.issued_date as achieved_date,
                c.id as reference_id
            FROM certificates cert
            JOIN courses c ON cert.course_id = c.id
            WHERE cert.user_id = ?
            ORDER BY cert.issued_date DESC
            LIMIT 3)
            UNION ALL
            (SELECT 
                'quiz_passed' as type,
                CONCAT('Passed quiz: ', q.title) as title,
                qr.completed_at as achieved_date,
                q.course_id as reference_id
            FROM quiz_results qr
            JOIN quizzes q ON qr.quiz_id = q.id
            WHERE qr.user_id = ? AND qr.passed = 1
            ORDER BY qr.completed_at DESC
            LIMIT 3)
            ORDER BY achieved_date DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id, $user_id, $user_id]);
        $recent_achievements = $stmt->fetchAll();

        $csrf_token = csrf_token();

        return view('academy.progress', get_defined_vars());
    }

    /** academy/notifications (was notifications.php) */
    public function notifications(Request $request)
    {
        extract(Academy::pageVars(false, 'notifications'));

        $page_title = 'Notifications';



        // Mark all as read if requested
        if (isset($_GET['mark_all_read'])) {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return ka_redirect('/notifications.php', 'All notifications marked as read', 'success');
        }

        // Get notifications with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $per_page, $offset]);
        $notifications = $stmt->fetchAll();

        // Get total count for pagination
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total = $stmt->fetchColumn();
        $total_pages = ceil($total / $per_page);

        // Get unread count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        $unread_count = $stmt->fetchColumn();

        return view('academy.notifications', get_defined_vars());
    }

    /** academy/quiz (was quiz.php) */
    public function quiz(Request $request)
    {
        extract(Academy::pageVars(false, 'quiz'));

        $page_title = 'Take Quiz';


        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        if (!$course_id) {
            return ka_redirect('/my-courses.php', 'Invalid course ID', 'danger');
        }

        // Get quiz details
        $stmt = $pdo->prepare("
            SELECT q.*, c.title as course_title, c.id as course_id
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE q.course_id = ? AND q.status = 'active'
        ");
        $stmt->execute([$course_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            return ka_redirect('/my-courses.php', 'Quiz not found or not active', 'danger');
        }

        // Check if user is enrolled in the course
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        if (!$stmt->fetch()) {
            return ka_redirect('/browse-courses.php', 'You must be enrolled in this course to take the quiz', 'danger');
        }

        // Get quiz questions
        $stmt = $pdo->prepare("
            SELECT * FROM quiz_questions 
            WHERE quiz_id = ? 
            ORDER BY order_number
        ");
        $stmt->execute([$quiz['id']]);
        $questions = $stmt->fetchAll();

        if (empty($questions)) {
            return ka_redirect('/course-player.php?id=' . $course_id, 'This quiz has no questions yet', 'warning');
        }

        // Shuffle questions if enabled
        if ($quiz['shuffle_questions']) {
            shuffle($questions);
        }

        // Check user's previous attempts
        $stmt = $pdo->prepare("
            SELECT * FROM quiz_results 
            WHERE user_id = ? AND quiz_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id, $quiz['id']]);
        $previous_attempts = $stmt->fetchAll();

        $attempts_count = count($previous_attempts);
        $can_take_quiz = true;
        $message = '';

        // Check if user has passed already
        foreach ($previous_attempts as $attempt) {
            if ($attempt['passed']) {
                $can_take_quiz = false;
                $message = 'You have already passed this quiz.';
                break;
            }
        }

        // Check attempts limit
        if ($can_take_quiz && $quiz['attempts_allowed'] > 0 && $attempts_count >= $quiz['attempts_allowed']) {
            $can_take_quiz = false;
            $message = 'You have reached the maximum number of attempts for this quiz.';
        }

        // Handle quiz submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } elseif (!$can_take_quiz) {
                $error = $message;
            } else {
                $answers = $_POST['answers'] ?? [];
                $total_questions = count($questions);
                $correct_count = 0;
                $results = [];

                foreach ($questions as $index => $question) {
                    $user_answer = $answers[$question['id']] ?? '';
                    $is_correct = ($user_answer === $question['correct_answer']);

                    if ($is_correct) {
                        $correct_count++;
                    }

                    $results[] = [
                        'question_id' => $question['id'],
                        'question' => $question['question'],
                        'user_answer' => $user_answer,
                        'correct_answer' => $question['correct_answer'],
                        'is_correct' => $is_correct,
                        'points' => $question['points'],
                        'explanation' => $question['explanation']
                    ];
                }

                $score = $correct_count;
                $percentage = ($correct_count / $total_questions) * 100;
                $passed = $percentage >= $quiz['passing_score'];

                // Save results
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO quiz_results (user_id, quiz_id, score, percentage, passed, answers, attempt_number, completed_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ");

                    $attempt_number = $attempts_count + 1;
                    $stmt->execute([
                        $user_id,
                        $quiz['id'],
                        $score,
                        $percentage,
                        $passed ? 1 : 0,
                        json_encode($results),
                        $attempt_number
                    ]);

                    $result_id = $pdo->lastInsertId();

                    // Log activity
                    ka_log_activity($pdo, $user_id, 'completed_quiz', [
                        'quiz_id' => $quiz['id'],
                        'score' => $score,
                        'percentage' => $percentage,
                        'passed' => $passed
                    ]);

                    // If passed, check if course is completed and generate certificate
                    if ($passed) {
                        // Check if all lessons are completed
                        $stmt = $pdo->prepare("
                            SELECT COUNT(*) as total,
                                   COUNT(CASE WHEN lp.completed = 1 THEN 1 END) as completed
                            FROM lessons l
                            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
                            WHERE l.course_id = ?
                        ");
                        $stmt->execute([$user_id, $course_id]);
                        $progress = $stmt->fetch();

                        if ($progress['total'] > 0 && $progress['completed'] == $progress['total']) {
                            // Course finished: the certificate waits for an admin's approval
                            Academy::requestCertificate($user_id, $course_id, (int) $result_id);
                        }
                    }

                    // Redirect to results page
                    return ka_redirect("/quiz-results.php?id=$result_id");

                } catch (Exception $e) {
                    $error = 'Failed to save quiz results: ' . $e->getMessage();
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.quiz', get_defined_vars());
    }

    /** academy/quiz-results (was quiz-results.php) */
    public function quizResults(Request $request)
    {
        extract(Academy::pageVars(false, 'quiz-results'));

        $page_title = 'Quiz Results';


        $result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$result_id) {
            return ka_redirect('/my-courses.php', 'Invalid result ID', 'danger');
        }

        // Get quiz result details
        $stmt = $pdo->prepare("
            SELECT qr.*, q.title as quiz_title, q.passing_score, q.show_answers,
                   c.id as course_id, c.title as course_title
            FROM quiz_results qr
            JOIN quizzes q ON qr.quiz_id = q.id
            JOIN courses c ON q.course_id = c.id
            WHERE qr.id = ? AND qr.user_id = ?
        ");
        $stmt->execute([$result_id, $user_id]);
        $result = $stmt->fetch();

        if (!$result) {
            return ka_redirect('/my-courses.php', 'Result not found', 'danger');
        }

        $answers = json_decode($result['answers'], true);
        $total_questions = count($answers);
        $correct_count = 0;
        foreach ($answers as $answer) {
            if ($answer['is_correct']) {
                $correct_count++;
            }
        }

        return view('academy.quiz-results', get_defined_vars());
    }

    /** academy/certificates (was certificates.php) */
    public function certificates(Request $request)
    {
        extract(Academy::pageVars(false, 'certificates'));

        $page_title = 'My Certificates';




        // Get all certificates with course details for this user
        $stmt = $pdo->prepare("
            SELECT cert.*, 
                   c.title as course_title, 
                   c.thumbnail as course_thumbnail,
                   u.full_name, 
                   u.employee_code,
                   qr.percentage as quiz_score,
                   qr.passed as quiz_passed
            FROM certificates cert
            JOIN courses c ON cert.course_id = c.id
            JOIN users u ON cert.user_id = u.id
            LEFT JOIN quiz_results qr ON cert.quiz_result_id = qr.id
            WHERE cert.user_id = ?
            ORDER BY cert.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $certificates = $stmt->fetchAll();

        // Ensure certificates is an array
        if (!is_array($certificates)) {
            $certificates = [];
        }

        // Get statistics
        $total_certificates = count($certificates);
        $approved_certificates = 0;
        $pending_certificates = 0;

        foreach ($certificates as $cert) {
            if (($cert['status'] ?? '') === 'approved') {
                $approved_certificates++;
            } elseif (($cert['status'] ?? '') === 'pending') {
                $pending_certificates++;
            }
        }

        // Get latest certificate date
        $latest_date = null;
        if (!empty($certificates)) {
            $latest_date = $certificates[0]['issued_date'] ?? $certificates[0]['created_at'];
        }

        return view('academy.certificates', get_defined_vars());
    }

    /** academy/certificate-pending (was certificate-pending.php) */
    public function certificatePending(Request $request)
    {
        extract(Academy::pageVars(false, 'certificate-pending'));

        $page_title = 'Certificate Pending';


        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        if (!$course_id) {
            return ka_redirect('/my-courses.php');
        }

        // Get certificate status - Fix: Select cert.* to get all certificate fields
        $stmt = $pdo->prepare("
            SELECT cert.*, 
                   crs.title as course_title,
                   u.full_name as user_name
            FROM certificates cert
            JOIN courses crs ON cert.course_id = crs.id
            JOIN users u ON cert.user_id = u.id
            WHERE cert.user_id = ? AND cert.course_id = ?
            ORDER BY cert.created_at DESC LIMIT 1
        ");
        $stmt->execute([$user_id, $course_id]);
        $certificate = $stmt->fetch();

        // If no certificate found or not pending, redirect
        if (!$certificate) {
            // Check if there's an approved certificate instead
            return ka_redirect("/certificates.php", "Certificate already approved or not found", "info");
        }

        // If certificate exists but status is not pending, handle accordingly
        if ($certificate['status'] != 'pending') {
            if ($certificate['status'] == 'approved') {
                return ka_redirect("/view-certificate.php?course_id=$course_id", "Your certificate has been approved!", "success");
            } elseif ($certificate['status'] == 'rejected') {
                // Stay on this page but show rejection message
                $show_rejection = true;
            } else {
                return ka_redirect("/my-courses.php");
            }
        }

        return view('academy.certificate-pending', get_defined_vars());
    }

    /** academy/download-certificate (was download-certificate.php) */
    public function downloadCertificate(Request $request)
    {
        extract(Academy::pageVars(false, 'download-certificate'));

        // Get certificate ID
        $certificate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$certificate_id) {
            return response('Invalid certificate ID', 404);
        }

        // Get certificate details and verify ownership
        $stmt = $pdo->prepare("
            SELECT cert.*, 
                   u.full_name, u.employee_code,
                   c.title as course_title, c.description as course_description
            FROM certificates cert
            JOIN users u ON cert.user_id = u.id
            JOIN courses c ON cert.course_id = c.id
            WHERE cert.id = ? AND cert.user_id = ?
        ");
        $stmt->execute([$certificate_id, $user_id]);
        $certificate = $stmt->fetch();

        if (!$certificate) {
            return response('Certificate not found or you do not have permission to download it', 404);
        }
        if ($certificate['status'] !== 'approved') {
            return ka_redirect('/certificate-pending.php?course_id=' . $certificate['course_id'], 'This certificate is still waiting for approval.', 'warning');
        }

        // Names printed on the certificate (config/academy.php, KA_CERT_* in .env)
        $admin_name = e(config('academy.certificate.signatory_name'));
        $admin_title = e(config('academy.certificate.signatory_title'));
        $institution_name = e(config('academy.certificate.institution_name'));
        $institution_address = e(config('academy.certificate.institution_address'));
        $institution_website = e(config('academy.certificate.institution_website'));
        $verify_url = e(url('academy/verify-certificate') . '?code=' . urlencode($certificate['certificate_number']));

        // Create PDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->set('dpi', 150);

        // Initialize DOMPDF
        $dompdf = new Dompdf($options);

        // Certificate HTML content
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Certificate of Completion</title>
            <style>
                @page {
                    margin: 0;
                    padding: 0;
                    size: landscape;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: "Helvetica", "Arial", sans-serif;
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .certificate-wrapper {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 40px;
                    box-sizing: border-box;
                }

                .certificate {
                    width: 1000px;
                    background: white;
                    border-radius: 40px;
                    padding: 50px;
                    box-shadow: 0 40px 60px rgba(0,0,0,0.3);
                    position: relative;
                    overflow: hidden;
                    border: 1px solid #e2e8f0;
                }

                .border-decoration {
                    position: absolute;
                    top: 20px;
                    left: 20px;
                    right: 20px;
                    bottom: 20px;
                    border: 3px solid transparent;
                    border-image: linear-gradient(135deg, #009ada, #76bc21);
                    border-image-slice: 1;
                    border-radius: 30px;
                    pointer-events: none;
                }

                .content {
                    position: relative;
                    z-index: 2;
                    text-align: center;
                }

                .badge {
                    position: absolute;
                    top: -10px;
                    right: -10px;
                    width: 120px;
                    height: 120px;
                    background: linear-gradient(135deg, #009ada, #76bc21);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                }

                .badge svg {
                    width: 60px;
                    height: 60px;
                    fill: white;
                }

                .header {
                    margin-bottom: 30px;
                }

                .title {
                    font-size: 70px;
                    font-weight: 800;
                    background: linear-gradient(135deg, #009ada, #76bc21);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    margin: 0;
                    line-height: 1;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                }

                .subtitle {
                    font-size: 18px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 8px;
                    margin-top: 5px;
                }

                .body {
                    margin: 40px 0;
                }

                .statement {
                    font-size: 20px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 3px;
                    margin-bottom: 20px;
                }

                .recipient-name {
                    font-size: 60px;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 20px 0;
                    text-transform: uppercase;
                    border-top: 3px solid #e2e8f0;
                    border-bottom: 3px solid #e2e8f0;
                    padding: 20px 0;
                    display: inline-block;
                }

                .employee-code {
                    font-size: 18px;
                    color: #64748b;
                    margin-bottom: 30px;
                }

                .employee-code span {
                    color: #009ada;
                    font-weight: 600;
                    background: #f0f9ff;
                    padding: 5px 15px;
                    border-radius: 30px;
                }

                .course-title {
                    font-size: 36px;
                    font-weight: 600;
                    color: #76bc21;
                    margin: 30px 0;
                    padding: 15px 30px;
                    background: #f0fdf4;
                    border-radius: 60px;
                    display: inline-block;
                }

                .date {
                    font-size: 18px;
                    color: #64748b;
                    margin: 30px 0;
                }

                .date strong {
                    color: #0f172a;
                    font-size: 22px;
                }

                .score {
                    background: #f8fafc;
                    padding: 10px 20px;
                    border-radius: 40px;
                    display: inline-block;
                    margin-top: 10px;
                }

                .score span {
                    color: #009ada;
                    font-weight: 600;
                }

                .footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    margin-top: 50px;
                    padding-top: 30px;
                    border-top: 2px solid #e2e8f0;
                }

                .signature {
                    text-align: center;
                    flex: 1;
                }

                .signature-line {
                    width: 250px;
                    height: 2px;
                    background: #0f172a;
                    margin: 10px auto;
                }

                .signature-name {
                    font-weight: 700;
                    color: #0f172a;
                    font-size: 18px;
                }

                .signature-title {
                    color: #64748b;
                    font-size: 14px;
                }

                .certificate-id {
                    text-align: right;
                }

                .certificate-id .label {
                    color: #64748b;
                    font-size: 12px;
                }

                .certificate-id .number {
                    font-family: monospace;
                    font-size: 16px;
                    font-weight: 600;
                    color: #0f172a;
                    background: #f1f5f9;
                    padding: 5px 10px;
                    border-radius: 6px;
                    letter-spacing: 1px;
                }

                .institution {
                    text-align: left;
                }

                .institution-name {
                    font-weight: 700;
                    color: #009ada;
                    font-size: 16px;
                }

                .institution-detail {
                    color: #64748b;
                    font-size: 12px;
                }

                .watermark {
                    position: absolute;
                    bottom: 50px;
                    right: 50px;
                    opacity: 0.1;
                    font-size: 150px;
                    color: #009ada;
                    transform: rotate(-15deg);
                    pointer-events: none;
                }

                .watermark i {
                    font-style: normal;
                }
            </style>
        </head>
        <body>
            <div class="certificate-wrapper">
                <div class="certificate">
                    <div class="border-decoration"></div>

                    <div class="badge">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2L15 9H22L16 14L19 21L12 16.5L5 21L8 14L2 9H9L12 2Z"/>
                        </svg>
                    </div>

                    <div class="content">
                        <!-- Header -->
                        <div class="header">
                            <h1 class="title">CERTIFICATE</h1>
                            <div class="subtitle">OF COMPLETION</div>
                        </div>

                        <!-- Body -->
                        <div class="body">
                            <div class="statement">THIS CERTIFICATE IS PROUDLY PRESENTED TO</div>

                            <div class="recipient-name">' . strtoupper(htmlspecialchars($certificate['full_name'])) . '</div>

                            <div class="employee-code">
                                <span>' . htmlspecialchars($certificate['employee_code']) . '</span>
                            </div>

                            <div class="statement">FOR SUCCESSFULLY COMPLETING THE COURSE</div>

                            <div class="course-title">' . strtoupper(htmlspecialchars($certificate['course_title'])) . '</div>

                            <div class="date">
                                on <strong>' . date('F j, Y', strtotime($certificate['issued_date'])) . '</strong>
                            </div>

                            <div class="score">
                                <i class="fa-solid fa-star"></i> Certificate ID: <span>' . $certificate['certificate_number'] . '</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="footer">
                            <div class="institution">
                                <div class="institution-name">' . $institution_name . '</div>
                                <div class="institution-detail">' . $institution_address . '</div>
                                <div class="institution-detail">' . $institution_website . '</div>
                                <div class="institution-detail">Verify: ' . $verify_url . '</div>
                            </div>

                            <div class="signature">
                                <div class="signature-line"></div>
                                <div class="signature-name">' . $admin_name . '</div>
                                <div class="signature-title">' . $admin_title . '</div>
                            </div>

                            <div class="certificate-id">
                                <div class="label">Certificate #</div>
                                <div class="number">' . $certificate['certificate_number'] . '</div>
                                <div style="margin-top: 10px;">
                                    <svg height="30" width="100">
                                        <rect x="0" y="0" width="100" height="30" style="fill:#f1f5f9;" />
                                        <text x="10" y="20" fill="#009ada" font-size="12">VERIFIED</text>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Watermark -->
                        <div class="watermark">
                            <i>KIBOKO</i>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';

        // Load HTML into DOMPDF
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF
        $dompdf->render();

        // Generate filename
        $filename = 'certificate_' . $certificate['certificate_number'] . '.pdf';

        // Output PDF for download
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** academy/generate-certificate (was generate-certificate.php) */
    public function generateCertificate(Request $request)
    {
        extract(Academy::pageVars(false, 'generate-certificate'));

        $page_title = 'Generate Certificate';


        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        if (!$course_id) {
            return ka_redirect('/my-courses.php', 'Invalid course ID', 'danger');
        }

        // Check if user has completed the course and passed the quiz
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                e.status as enrollment_status,
                e.completed_at,
                COUNT(DISTINCT l.id) as total_lessons,
                COUNT(DISTINCT lp.id) as completed_lessons,
                q.id as quiz_id,
                qr.passed as quiz_passed,
                qr.percentage as quiz_score,
                qr.id as quiz_result_id,
                cert.id as certificate_id,
                cert.certificate_number,
                cert.issued_date,
                cert.pdf_path,
                cert.status as certificate_status
            FROM courses c
            JOIN enrollments e ON c.id = e.course_id AND e.user_id = ?
            LEFT JOIN lessons l ON c.id = l.course_id
            LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = e.user_id AND lp.completed = 1
            LEFT JOIN quizzes q ON c.id = q.course_id
            LEFT JOIN quiz_results qr ON q.id = qr.quiz_id AND qr.user_id = e.user_id AND qr.passed = 1
            LEFT JOIN certificates cert ON c.id = cert.course_id AND cert.user_id = e.user_id
            WHERE c.id = ? AND e.user_id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$user_id, $course_id, $user_id]);
        $data = $stmt->fetch();

        if (!$data) {
            return ka_redirect('/my-courses.php', 'Course not found or you are not enrolled', 'danger');
        }

        // Check if course is completed
        $lessons_completed = ($data['total_lessons'] > 0 && $data['completed_lessons'] == $data['total_lessons']);
        $course_completed = $lessons_completed && $data['quiz_passed'];

        if (!$course_completed) {
            $message = '';
            if (!$lessons_completed) {
                $message = 'You need to complete all lessons first.';
            } elseif (!$data['quiz_passed']) {
                $message = 'You need to pass the quiz first.';
            }

            ka_flash('You have not completed all requirements for this course. ' . $message, 'warning');
            return ka_redirect("/course-player.php?id=$course_id");
        }

        // Get user details
        $stmt = $pdo->prepare("SELECT full_name, employee_code, email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // Check if certificate already exists
        if (!$data['certificate_id']) {
            // Generate new certificate number
            $certificate_number = 'CERT-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $issued_date = date('Y-m-d');

            // Insert pending certificate record
            $stmt = $pdo->prepare("
                INSERT INTO certificates (
                    certificate_number, user_id, course_id, quiz_result_id, 
                    issued_date, status
                ) VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $certificate_number, 
                $user_id, 
                $course_id, 
                $data['quiz_result_id'], 
                $issued_date
            ]);

            $certificate_id = $pdo->lastInsertId();

            // Create notification for all admins
            $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
            $stmt->execute();
            $admins = $stmt->fetchAll();

            foreach ($admins as $admin) {
                $notification_message = "New certificate pending approval for {$user['full_name']} - Course: {$data['title']}";
                $notification_link = "admin/approve-certificate.php?id={$certificate_id}";

                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, title, message, type, link, created_at) 
                    VALUES (?, 'Certificate Approval Required', ?, 'warning', ?, NOW())
                ");
                $stmt->execute([$admin['id'], $notification_message, $notification_link]);
            }

            // Log activity
            ka_log_activity($pdo, $user_id, 'requested_certificate', [
                'course_id' => $course_id,
                'certificate_number' => $certificate_number,
                'status' => 'pending'
            ]);

            // Show pending message
            ka_flash('Your certificate has been submitted for approval. You will be notified once approved.', 'info');
            return ka_redirect("/certificate-pending.php?course_id=$course_id");

        } else if ($data['certificate_status'] == 'pending') {
            // Certificate already pending
            ka_flash('Your certificate is pending approval. You will be notified once approved.', 'info');
            return ka_redirect("/certificate-pending.php?course_id=$course_id");

        } else if ($data['certificate_status'] == 'approved') {
            // Certificate is already approved - show it
            return ka_redirect("/view-certificate.php?course_id=$course_id");

        } else if ($data['certificate_status'] == 'rejected') {
            // Certificate was rejected
            ka_flash('Your certificate request was rejected. Please contact administrator.', 'danger');
            return ka_redirect("/my-courses.php");
        }

        return response('');
    }

    /** academy/verify-certificate (was verify-certificate.php) */
    public function verifyCertificate(Request $request)
    {
        extract(Academy::publicVars('verify-certificate'));

        $page_title = 'Verify Certificate';
        $certificate_code = isset($_GET['code']) ? trim($_GET['code']) : '';

        $certificate = null;
        $valid = false;

        if (!empty($certificate_code)) {
            // Get certificate details
            $stmt = $pdo->prepare("
                SELECT cert.*, 
                       u.full_name, u.employee_code,
                       c.title as course_title, c.description as course_description
                FROM certificates cert
                JOIN users u ON cert.user_id = u.id
                JOIN courses c ON cert.course_id = c.id
                WHERE cert.certificate_number = ?
            ");
            $stmt->execute([$certificate_code]);
            $certificate = $stmt->fetch();

            // Only certificates an admin approved are genuine
            if ($certificate && $certificate['status'] === 'approved') {
                $valid = true;
            } else {
                $certificate = null;
            }
        }

        return view('academy.verify-certificate', get_defined_vars());
    }

    /** academy/profile (was profile.php) */
    public function profile(Request $request)
    {
        extract(Academy::pageVars(false, 'profile'));

        $page_title = 'My Profile';



        $error = '';
        $success = '';

        // Get user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // Handle profile update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!ka_verify_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                // Name and e-mail come from the K-Connect employee record (Academy::linkEmployee()),
                // so only the profile photo is changed here.
                $full_name = $user['full_name'];
                $email = $user['email'];

                if (empty($error)) {
                    // Handle profile image upload
                    $profile_image = $user['profile_image'];
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        $validation = ka_validate_file_upload($_FILES['profile_image'], ['jpg', 'jpeg', 'png', 'gif'], 2097152);
                        if ($validation['valid']) {
                            $ext = $validation['extension'];
                            $filename = 'user_' . $user_id . '_' . uniqid() . '.' . $ext;
                            $upload_path = 'assets/uploads/profiles/' . $filename;

                            ka_ensure_dir('assets/uploads/profiles/');

                            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                                // Delete old image if exists
                                if ($profile_image && file_exists('assets/uploads/profiles/' . $profile_image)) {
                                    unlink('assets/uploads/profiles/' . $profile_image);
                                }
                                $profile_image = $filename;
                            }
                        } else {
                            $error = $validation['error'];
                        }
                    }

                    if (empty($error)) {
                        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_image = ? WHERE id = ?");
                        if ($stmt->execute([$full_name, $email, $profile_image, $user_id])) {
                            $user_name = $full_name;
                            $success = 'Profile updated successfully';

                            // Refresh user data
                            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                            $stmt->execute([$user_id]);
                            $user = $stmt->fetch();
                        }
                    }
                }
            }
        }

        $csrf_token = csrf_token();

        return view('academy.profile', get_defined_vars());
    }

}
