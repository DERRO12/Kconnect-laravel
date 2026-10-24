<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Support\Academy;
use Exception;
use Illuminate\Http\Request;
use PDO;
use PDOException;

/**
 * K-Academy AJAX endpoints (were kacademy/api/*.php).
 * The page code is the old K-Academy code on the "kacademy" database connection; it uses
 * $pdo (Academy::pdo()), $user_id/$user (Academy::pageVars()) and the ka_* helpers.
 */
class ApiController extends Controller
{
    /** academy/api/check-certificate-status (was api/check-certificate-status.php) */
    public function checkCertificateStatus(Request $request)
    {
        extract(Academy::pageVars(false, 'check-certificate-status'));

        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

        if (!$course_id) {
            return kc_json(['error' => 'Invalid course ID']);
        }

        $stmt = $pdo->prepare("
            SELECT status, rejection_reason 
            FROM certificates 
            WHERE user_id = ? AND course_id = ?
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$user_id, $course_id]);
        $certificate = $stmt->fetch();

        if ($certificate) {
            return kc_json([
                'status' => $certificate['status'],
                'rejection_reason' => $certificate['rejection_reason']
            ]);
        } else {
            return kc_json(['status' => 'not_found']);
        }

        return response('');
    }

    /** academy/api/clear-notifications (was api/clear-notifications.php) */
    public function clearNotifications(Request $request)
    {
        extract(Academy::pageVars(false, 'clear-notifications'));

        // Delete all notifications for user
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->execute([$user_id]);

        return ka_redirect('/notifications.php', 'All notifications cleared', 'success');

        return response('');
    }

    /** academy/api/complete-lesson (was api/complete-lesson.php) */
    public function completeLesson(Request $request)
    {
        extract(Academy::pageVars(false, 'complete-lesson'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $lesson_id = $data['lesson_id'] ?? 0;
        $completed = $data['completed'] ?? false;

        // Get lesson details
        $stmt = $pdo->prepare("SELECT course_id FROM lessons WHERE id = ?");
        $stmt->execute([$lesson_id]);
        $lesson = $stmt->fetch();

        if (!$lesson) {
            return kc_json(['error' => 'Lesson not found']);
        }

        // Update progress
        $stmt = $pdo->prepare("
            INSERT INTO lesson_progress (user_id, lesson_id, course_id, completed, completed_at, last_accessed)
            VALUES (?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
            completed = VALUES(completed),
            completed_at = CASE WHEN VALUES(completed) = 1 THEN NOW() ELSE NULL END,
            last_accessed = NOW()
        ");
        $stmt->execute([$user_id, $lesson_id, $lesson['course_id'], $completed ? 1 : 0]);

        // Calculate overall course progress
        $progress = ka_calculate_course_progress($pdo, $user_id, $lesson['course_id']);

        // Check if course is completed and generate certificate if needed
        if ($progress >= 100) {
            // Check if user has passed the quiz
            $stmt = $pdo->prepare("
                SELECT qr.* FROM quiz_results qr
                JOIN quizzes q ON qr.quiz_id = q.id
                WHERE qr.user_id = ? AND q.course_id = ? AND qr.passed = 1
                ORDER BY qr.created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$user_id, $lesson['course_id']]);
            $quiz_result = $stmt->fetch();

            if ($quiz_result) {
                // All lessons done and the quiz passed: ask for the certificate (an admin approves it)
                Academy::requestCertificate($user_id, (int) $lesson['course_id'], (int) $quiz_result['id']);
            }
        }

        return kc_json([
            'success' => true,
            'progress' => $progress,
            'course_completed' => $progress >= 100
        ]);

        return response('');
    }

    /** academy/api/delete-comment (was api/delete-comment.php) */
    public function deleteComment(Request $request)
    {
        extract(Academy::pageVars(false, 'delete-comment'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comment_id = $data['comment_id'] ?? 0;
        $token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        try {
            // Check if user owns the comment
            $stmt = $pdo->prepare("SELECT user_id FROM discussions WHERE id = ?");
            $stmt->execute([$comment_id]);
            $owner_id = $stmt->fetchColumn();

            if ($owner_id != $user_id) {
                return kc_json(['success' => false, 'message' => 'You can only delete your own comments']);
            }

            // Delete comment (cascade will delete replies)
            $stmt = $pdo->prepare("DELETE FROM discussions WHERE id = ?");
            $stmt->execute([$comment_id]);

            ka_log_activity($pdo, $user_id, 'deleted_comment', ['comment_id' => $comment_id]);

            return kc_json(['success' => true, 'message' => 'Comment deleted successfully']);
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/delete-discussion (was api/delete-discussion.php) */
    public function deleteDiscussion(Request $request)
    {
        extract(Academy::pageVars(false, 'delete-discussion'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return kc_json(['success' => false, 'message' => 'Invalid input data']);
        }

        if (!isset($input['csrf_token']) || !ka_verify_csrf($input['csrf_token'])) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        $discussion_id = isset($input['discussion_id']) ? (int)$input['discussion_id'] : 0;

        if (!$discussion_id) {
            return kc_json(['success' => false, 'message' => 'Invalid discussion ID']);
        }

        // Verify user owns this discussion
        $stmt = $pdo->prepare("SELECT user_id FROM discussions WHERE id = ?");
        $stmt->execute([$discussion_id]);
        $discussion = $stmt->fetch();

        if (!$discussion || $discussion['user_id'] != $user_id) {
            return kc_json(['success' => false, 'message' => 'You can only delete your own discussions']);
        }

        try {
            // Delete likes first (if likes table exists)
            $pdo->prepare("DELETE FROM discussion_likes WHERE discussion_id = ?")->execute([$discussion_id]);

            // Delete the discussion (replies will cascade if foreign key is set)
            $stmt = $pdo->prepare("DELETE FROM discussions WHERE id = ?");

            if ($stmt->execute([$discussion_id])) {
                ka_log_activity($pdo, $user_id, 'deleted_discussion', [
                    'discussion_id' => $discussion_id
                ]);

                return kc_json(['success' => true]);
            } else {
                return kc_json(['success' => false, 'message' => 'Failed to delete discussion']);
            }
        } catch (PDOException $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/delete-reply (was api/delete-reply.php) */
    public function deleteReply(Request $request)
    {
        extract(Academy::pageVars(false, 'delete-reply'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return kc_json(['success' => false, 'message' => 'Invalid input data']);
        }

        if (!isset($input['csrf_token']) || !ka_verify_csrf($input['csrf_token'])) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        $reply_id = isset($input['reply_id']) ? (int)$input['reply_id'] : 0;

        if (!$reply_id) {
            return kc_json(['success' => false, 'message' => 'Invalid reply ID']);
        }

        // Verify user owns this reply
        $stmt = $pdo->prepare("SELECT user_id FROM discussions WHERE id = ?");
        $stmt->execute([$reply_id]);
        $reply = $stmt->fetch();

        if (!$reply || $reply['user_id'] != $user_id) {
            return kc_json(['success' => false, 'message' => 'You can only delete your own replies']);
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM discussions WHERE id = ?");

            if ($stmt->execute([$reply_id])) {
                ka_log_activity($pdo, $user_id, 'deleted_reply', [
                    'reply_id' => $reply_id
                ]);

                return kc_json(['success' => true]);
            } else {
                return kc_json(['success' => false, 'message' => 'Failed to delete reply']);
            }
        } catch (PDOException $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/enroll-course (was api/enroll-course.php) */
    public function enrollCourse(Request $request)
    {
        extract(Academy::pageVars(false, 'enroll-course'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $course_id = $data['course_id'] ?? 0;
        $csrf_token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($csrf_token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Course must exist and be published
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? AND status = 'published'");
        $stmt->execute([$course_id]);
        if (!$stmt->fetch()) {
            return kc_json(['success' => false, 'message' => 'Course not found']);
        }

        // Check if already enrolled
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);

        if ($stmt->fetch()) {
            return kc_json(['success' => false, 'message' => 'Already enrolled in this course']);
        }

        // Enroll user
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at, status) VALUES (?, ?, NOW(), 'in_progress')");
        if ($stmt->execute([$user_id, $course_id])) {
            ka_log_activity($pdo, $user_id, 'enrolled_course', ['course_id' => $course_id]);

            return kc_json([
                'success' => true,
                'message' => 'Successfully enrolled in course'
            ]);
        } else {
            return kc_json(['success' => false, 'message' => 'Failed to enroll']);
        }

        return response('');
    }

    /** academy/api/get-attempts (was api/get-attempts.php) */
    public function getAttempts(Request $request)
    {
        extract(Academy::pageVars(false, 'get-attempts'));

        $quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

        // Get quiz details
        $stmt = $pdo->prepare("SELECT attempts_allowed FROM quizzes WHERE id = ?");
        $stmt->execute([$quiz_id]);
        $allowed = $stmt->fetchColumn();

        // Count attempts
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempts,
                   MAX(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed
            FROM quiz_results 
            WHERE user_id = ? AND quiz_id = ?
        ");
        $stmt->execute([$user_id, $quiz_id]);
        $result = $stmt->fetch();

        return kc_json([
            'attempts' => $result['attempts'],
            'allowed' => $allowed ?: 'Unlimited',
            'passed' => (bool)$result['passed']
        ]);

        return response('');
    }

    /** academy/api/like-comment (was api/like-comment.php) */
    public function likeComment(Request $request)
    {
        extract(Academy::pageVars(false, 'like-comment'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comment_id = $data['comment_id'] ?? 0;
        $token = $data['csrf_token'] ?? '';

        if (!ka_verify_csrf($token)) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        try {
            // Increment likes
            $stmt = $pdo->prepare("UPDATE discussions SET likes = likes + 1 WHERE id = ?");
            $stmt->execute([$comment_id]);

            // Get new like count
            $stmt = $pdo->prepare("SELECT likes FROM discussions WHERE id = ?");
            $stmt->execute([$comment_id]);
            $likes = $stmt->fetchColumn();

            ka_log_activity($pdo, $user_id, 'liked_comment', ['comment_id' => $comment_id]);

            return kc_json(['success' => true, 'likes' => $likes]);
        } catch (Exception $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/post-discussion (was api/post-discussion.php) */
    public function postDiscussion(Request $request)
    {
        extract(Academy::pageVars(false, 'post-discussion'));

        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return kc_json(['success' => false, 'message' => 'Invalid input data']);
        }

        // Verify CSRF token
        if (!isset($input['csrf_token']) || !ka_verify_csrf($input['csrf_token'])) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        // Validate input
        $lesson_id = isset($input['lesson_id']) ? (int)$input['lesson_id'] : 0;
        $comment = isset($input['comment']) ? trim($input['comment']) : '';

        if (!$lesson_id) {
            return kc_json(['success' => false, 'message' => 'Please select a lesson']);
        }

        if (empty($comment)) {
            return kc_json(['success' => false, 'message' => 'Please write a comment']);
        }

        // Insert discussion
        try {
            $stmt = $pdo->prepare("
                INSERT INTO discussions (user_id, lesson_id, comment, created_at) 
                VALUES (?, ?, ?, NOW())
            ");

            if ($stmt->execute([$user_id, $lesson_id, $comment])) {
                $discussion_id = $pdo->lastInsertId();

                // Log activity
                ka_log_activity($pdo, $user_id, 'posted_discussion', [
                    'discussion_id' => $discussion_id,
                    'lesson_id' => $lesson_id
                ]);

                return kc_json(['success' => true, 'discussion_id' => $discussion_id]);
            } else {
                return kc_json(['success' => false, 'message' => 'Failed to post discussion']);
            }
        } catch (PDOException $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/post-reply (was api/post-reply.php) */
    public function postReply(Request $request)
    {
        extract(Academy::pageVars(false, 'post-reply'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return kc_json(['success' => false, 'message' => 'Invalid input data']);
        }

        if (!isset($input['csrf_token']) || !ka_verify_csrf($input['csrf_token'])) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        $discussion_id = isset($input['discussion_id']) ? (int)$input['discussion_id'] : 0;
        $comment = isset($input['comment']) ? trim($input['comment']) : '';

        if (!$discussion_id) {
            return kc_json(['success' => false, 'message' => 'Invalid discussion ID']);
        }

        if (empty($comment)) {
            return kc_json(['success' => false, 'message' => 'Please write a reply']);
        }

        // Get the lesson_id from parent discussion
        $stmt = $pdo->prepare("SELECT lesson_id FROM discussions WHERE id = ?");
        $stmt->execute([$discussion_id]);
        $parent = $stmt->fetch();

        if (!$parent) {
            return kc_json(['success' => false, 'message' => 'Parent discussion not found']);
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO discussions (user_id, lesson_id, parent_id, comment, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            if ($stmt->execute([$user_id, $parent['lesson_id'], $discussion_id, $comment])) {
                $reply_id = $pdo->lastInsertId();

                ka_log_activity($pdo, $user_id, 'posted_reply', [
                    'reply_id' => $reply_id,
                    'discussion_id' => $discussion_id
                ]);

                return kc_json(['success' => true, 'reply_id' => $reply_id]);
            } else {
                return kc_json(['success' => false, 'message' => 'Failed to post reply']);
            }
        } catch (PDOException $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/toggle-like (was api/toggle-like.php) */
    public function toggleLike(Request $request)
    {
        extract(Academy::pageVars(false, 'toggle-like'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return kc_json(['success' => false, 'message' => 'Invalid input data']);
        }

        if (!isset($input['csrf_token']) || !ka_verify_csrf($input['csrf_token'])) {
            return kc_json(['success' => false, 'message' => 'Invalid security token']);
        }

        $discussion_id = isset($input['discussion_id']) ? (int)$input['discussion_id'] : 0;

        if (!$discussion_id) {
            return kc_json(['success' => false, 'message' => 'Invalid discussion ID']);
        }

        // Check if user already liked
        $stmt = $pdo->prepare("SELECT id FROM discussion_likes WHERE user_id = ? AND discussion_id = ?");
        $stmt->execute([$user_id, $discussion_id]);
        $existing_like = $stmt->fetch();

        try {
            if ($existing_like) {
                // Unlike
                $stmt = $pdo->prepare("DELETE FROM discussion_likes WHERE user_id = ? AND discussion_id = ?");
                $stmt->execute([$user_id, $discussion_id]);

                // Decrement likes count in discussions table
                $stmt = $pdo->prepare("UPDATE discussions SET likes = likes - 1 WHERE id = ?");
                $stmt->execute([$discussion_id]);

                $liked = false;
            } else {
                // Like
                $stmt = $pdo->prepare("INSERT INTO discussion_likes (user_id, discussion_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $discussion_id]);

                // Increment likes count in discussions table
                $stmt = $pdo->prepare("UPDATE discussions SET likes = likes + 1 WHERE id = ?");
                $stmt->execute([$discussion_id]);

                $liked = true;
            }

            // Get updated likes count
            $stmt = $pdo->prepare("SELECT likes FROM discussions WHERE id = ?");
            $stmt->execute([$discussion_id]);
            $likes = $stmt->fetchColumn();

            return kc_json(['success' => true, 'liked' => $liked, 'likes' => $likes]);

        } catch (PDOException $e) {
            return kc_json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

        return response('');
    }

    /** academy/api/update-progress (was api/update-progress.php) */
    public function updateProgress(Request $request)
    {
        extract(Academy::pageVars(false, 'update-progress'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return kc_json(['error' => 'Method not allowed'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $lesson_id = $data['lesson_id'] ?? 0;
        $watched_seconds = $data['watched_seconds'] ?? 0;

        // Get lesson details
        $stmt = $pdo->prepare("SELECT course_id, duration FROM lessons WHERE id = ?");
        $stmt->execute([$lesson_id]);
        $lesson = $stmt->fetch();

        if (!$lesson) {
            return kc_json(['error' => 'Lesson not found']);
        }

        // Update or insert progress
        $stmt = $pdo->prepare("
            INSERT INTO lesson_progress (user_id, lesson_id, course_id, watched_duration, last_accessed)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            watched_duration = VALUES(watched_duration),
            last_accessed = NOW(),
            completed = CASE 
                WHEN VALUES(watched_duration) >= ? THEN 1 
                ELSE completed 
            END
        ");
        $stmt->execute([
            $user_id,
            $lesson_id,
            $lesson['course_id'],
            $watched_seconds,
            $lesson['duration'] * 60 // Convert minutes to seconds
        ]);

        // Calculate overall course progress
        $progress = ka_calculate_course_progress($pdo, $user_id, $lesson['course_id']);

        return kc_json([
            'success' => true,
            'progress' => $progress
        ]);

        return response('');
    }

    /*
    | Endpoints the old pages called but the old site never had (or had as empty files).
    */

    /** academy/api/mark-notifications-read: the bell menu's "Mark all as read" */
    public function markNotificationsRead(Request $request)
    {
        $user = Academy::requireLearner();
        Academy::db()->update('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0', [$user['id']]);

        return response()->json(['success' => true]);
    }

    /** academy/api/get-notes?lesson_id= : the learner's notes for one lesson */
    public function getNotes(Request $request)
    {
        $user = Academy::requireLearner();
        $row = Academy::db()->selectOne('SELECT notes, updated_at FROM lesson_notes WHERE user_id = ? AND lesson_id = ?', [$user['id'], (int) $request->query('lesson_id')]);

        return response()->json([
            'success' => true,
            'notes' => $row['notes'] ?? '',
            'updated_at' => $row ? date('M d, Y g:i A', strtotime($row['updated_at'])) : null,
        ]);
    }

    /** academy/api/save-notes (JSON: lesson_id, notes) */
    public function saveNotes(Request $request)
    {
        $user = Academy::requireLearner();
        $lessonId = (int) $request->json('lesson_id');
        $notes = (string) $request->json('notes', '');
        $ka = Academy::db();
        if (! $lessonId || ! $ka->selectOne('SELECT id FROM lessons WHERE id = ?', [$lessonId])) {
            return response()->json(['success' => false, 'message' => 'Lesson not found']);
        }
        $ka->statement('INSERT INTO lesson_notes (user_id, lesson_id, notes) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE notes = VALUES(notes), updated_at = NOW()', [$user['id'], $lessonId, $notes]);

        return response()->json(['success' => true]);
    }

    /** academy/api/export-all-notes?course_id= (and backup-notes): all notes of a course as a text file */
    public function exportAllNotes(Request $request)
    {
        $user = Academy::requireLearner();
        $ka = Academy::db();
        $courseId = (int) $request->query('course_id');
        $course = $ka->selectOne('SELECT title FROM courses WHERE id = ?', [$courseId]);
        if (! $course) {
            abort(404);
        }
        $rows = $ka->select('SELECT l.order_number, l.title, ln.notes, ln.updated_at FROM lessons l
            JOIN lesson_notes ln ON ln.lesson_id = l.id AND ln.user_id = ?
            WHERE l.course_id = ? AND ln.notes IS NOT NULL AND ln.notes <> \'\' ORDER BY l.order_number', [$user['id'], $courseId]);
        $text = 'My notes: '.$course['title']."\r\n".$user['full_name'].' - '.date('M d, Y')."\r\n\r\n";
        foreach ($rows as $row) {
            $text .= 'Lesson '.$row['order_number'].': '.$row['title']."\r\n".str_repeat('-', 40)."\r\n".$row['notes']."\r\n\r\n";
        }
        if (! $rows) {
            $text .= "No notes yet.\r\n";
        }
        $name = 'notes-'.(ka_slug($course['title']) ?: 'course').'.txt';

        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$name.'"']);
    }

    /** academy/api/unenroll-course?id= : "Leave course" on My Courses */
    public function unenrollCourse(Request $request)
    {
        $user = Academy::requireLearner();
        $courseId = (int) $request->input('id');
        if (! ka_verify_csrf((string) $request->input('token'))) {
            ka_flash('Your session expired. Please try again.', 'danger');

            return redirect(url('academy/my-courses'));
        }
        $ka = Academy::db();
        if ($ka->delete('DELETE FROM enrollments WHERE user_id = ? AND course_id = ?', [$user['id'], $courseId])) {
            $ka->delete('DELETE FROM lesson_progress WHERE user_id = ? AND course_id = ?', [$user['id'], $courseId]);
            Academy::log((int) $user['id'], 'unenrolled_course', ['course_id' => $courseId]);
            ka_flash('You have left the course.', 'success');
        }

        return redirect(url('academy/my-courses'));
    }

    /** academy/api/get-course-lessons?course_id= : lesson list on My Progress */
    public function getCourseLessons(Request $request)
    {
        $user = Academy::requireLearner();
        $lessons = Academy::db()->select('SELECT l.id, l.title, l.duration, l.video_type, COALESCE(lp.completed, 0) AS completed
            FROM lessons l LEFT JOIN lesson_progress lp ON lp.lesson_id = l.id AND lp.user_id = ?
            WHERE l.course_id = ? ORDER BY l.order_number', [$user['id'], (int) $request->query('course_id')]);
        foreach ($lessons as &$lesson) {
            $lesson['completed'] = (bool) $lesson['completed'];
        }
        unset($lesson);

        return response()->json(['success' => true, 'lessons' => $lessons]);
    }

    /** academy/api/upload-editor-image : picture pasted into the course description editor (admins) */
    public function uploadEditorImage(Request $request)
    {
        Academy::requireAdmin();
        $file = $request->file('file');
        if (! $file || ! $file->isValid() || $file->getSize() > 5 * 1024 * 1024
            || ! in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return response('Invalid image', 422);
        }
        $name = uniqid().'_'.time().'.'.strtolower($file->getClientOriginalExtension());
        $file->move(Academy::filePath('courses'), $name);

        return response(Academy::fileUrl('courses', $name));
    }
}
