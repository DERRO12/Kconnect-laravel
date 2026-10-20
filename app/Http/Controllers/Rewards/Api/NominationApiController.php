<?php

namespace App\Http\Controllers\Rewards\Api;

use App\Http\Controllers\Controller;
use App\Support\NominationsLegacy;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** JSON endpoints used by the rewards/ nomination pages (old rewards/api/*.php). */
class NominationApiController extends Controller
{
    /** Old: current quarter settings, or the quarter of today's date */
    private function settings(bool $withUnique = false): array
    {
        $cols = $withUnique ? 'current_quarter, current_year, unique_votes_per_quarter' : 'current_quarter, current_year';
        $settings = DB::selectOne("SELECT $cols FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1");
        if ($settings) {
            return [$settings['current_quarter'], $settings['current_year'], $settings['unique_votes_per_quarter'] ?? 0];
        }

        return [ceil(date('n') / 3), date('Y'), 0];
    }

    /** Decisions allowed per committee level (nominations.levelN_decision enums) */
    private const LEVEL_DECISIONS = [
        1 => ['pending', 'shortlisted', 'rejected'],
        2 => ['pending', 'shortlisted', 'rejected'],
        3 => ['pending', 'approved', 'rejected'],
    ];

    /**
     * The committee endpoints are only for logged-in active members of that committee level
     * (or a logged-in admin). Returns the level, or stops with a JSON error.
     */
    private function committeeLevel(Request $request, $level): int
    {
        $level = (int) $level;
        if (!isset(self::LEVEL_DECISIONS[$level])) {
            NominationsLegacy::failJson('Invalid committee level', null, 422);
        }
        if (isAdminLoggedIn()) {
            return $level;
        }
        $employee_id = (int) (session('employee_id') ?? 0);
        if (!$employee_id) {
            NominationsLegacy::failJson('User not logged in', null, 401);
        }
        $member = DB::selectOne('SELECT id FROM nomination_committee WHERE employee_id = ? AND committee_level = ? AND status = 1', [$employee_id, $level]);
        if (!$member) {
            NominationsLegacy::failJson('You are not authorized for this level', null, 403);
        }

        return $level;
    }

    /** Whether nomination_categories has a column (cached; the shipped schema has no quarter_winner_* columns). */
    private function categoryHasColumn(string $column): bool
    {
        static $memo = [];

        return $memo[$column] ??= (bool) Cache::remember(
            'kc_schema:'.DB::getDatabaseName().':nomination_categories.'.$column,
            3600,
            fn () => Schema::hasColumn('nomination_categories', $column) ? 1 : 0
        );
    }

    /** rewards/api/check_nomination */
    public function checkNomination(Request $request)
    {
        $category_id = $request->query('category_id') !== null ? (int) $request->query('category_id') : 0;
        $nominator_id = session()->has('employee_id') ? (int) session('employee_id') : 0;

        if (!$category_id || !$nominator_id) {
            return kc_json(['has_existing' => false]);
        }

        // Since we allow multiple nominations, always return false
        return kc_json(['has_existing' => false]);
    }

    /** rewards/api/get_categories */
    public function getCategories(Request $request)
    {
        $user_id = session()->has('employee_id') ? (int) session('employee_id') : 0;

        // Get current quarter settings
        [$current_quarter, $current_year, $unique_votes_enabled] = $this->settings(true);

        // Check if tracking table exists
        $tracking_table_exists = count(DB::select("SHOW TABLES LIKE 'nomination_user_tracking'")) > 0;

        $result = DB::select('
    SELECT
        c.id,
        c.name,
        c.description,
        c.icon,
        c.color,
        c.is_self_nomination
    FROM nomination_categories c
    WHERE c.status = 1
    ORDER BY c.sort_order
');

        $categories = [];
        foreach ($result as $row) {
            // Check if user has already voted in this category this quarter
            $row['user_has_voted'] = false;
            $row['quarter_winner_id'] = null;  // Add default values for these fields
            $row['quarter_winner_quarter'] = null;
            $row['quarter_winner_year'] = null;
            $row['winner_info'] = null;

            // Get winner info from quarter_winners table instead
            $winner = DB::selectOne('
        SELECT qw.nominee_id, e.full_name, e.employee_code, qw.winner_rank
        FROM quarter_winners qw
        JOIN employees e ON qw.nominee_id = e.id
        WHERE qw.category_id = ?
        AND qw.quarter = ?
        AND qw.year = ?
        AND qw.winner_rank = 1
        LIMIT 1
    ', [$row['id'], $current_quarter, $current_year]);
            if ($winner) {
                $row['winner_info'] = $winner;
                $row['quarter_winner_id'] = $row['winner_info']['nominee_id'] ?? null;
            }

            if ($unique_votes_enabled == 1 && $user_id > 0) {
                // First check the tracking table (if it exists)
                $has_tracking_vote = false;
                if ($tracking_table_exists) {
                    $has_tracking_vote = count(DB::select('
                SELECT id FROM nomination_user_tracking
                WHERE user_id = ?
                AND category_id = ?
                AND quarter = ?
                AND year = ?
            ', [$user_id, $row['id'], $current_quarter, $current_year])) > 0;
                }

                // Also check existing nominations (retroactive check)
                $existing_nomination = DB::select("
            SELECT id FROM nominations
            WHERE nominator_id = ?
            AND category_id = ?
            AND quarter = ?
            AND year = ?
            AND status != 'rejected'
        ", [$user_id, $row['id'], $current_quarter, $current_year]);
                $has_existing_nomination = count($existing_nomination) > 0;

                // User has voted if either tracking exists OR they have an existing nomination
                $row['user_has_voted'] = $has_tracking_vote || $has_existing_nomination;

                // If they have an existing nomination but no tracking record, sync them
                if ($has_existing_nomination && !$has_tracking_vote && $tracking_table_exists) {
                    $nom_data = $existing_nomination[0] ?? null;
                    if ($nom_data) {
                        DB::insert('
                    INSERT IGNORE INTO nomination_user_tracking (user_id, category_id, quarter, year, nomination_id)
                    VALUES (?, ?, ?, ?, ?)
                ', [$user_id, $row['id'], $current_quarter, $current_year, $nom_data['id']]);
                    }
                }
            }

            $categories[] = $row;
        }

        return kc_json([
            'success' => true,
            'data' => $categories,
            'current_quarter' => $current_quarter,
            'current_year' => $current_year,
            'unique_votes_enabled' => ($unique_votes_enabled == 1)
        ]);
    }

    /** rewards/api/get_committee_nominations */
    public function getCommitteeNominations(Request $request)
    {
        $level = $this->committeeLevel($request, $request->query('level') ?? 1);
        $category_id = $request->query('category') !== null ? (int) $request->query('category') : 0;

        // Get current quarter/year
        [$current_quarter, $current_year] = $this->settings();

        $category_filter = $category_id > 0 ? "AND n.category_id = $category_id" : '';

        // Get nominations pending for this level
        $decision_field = "level{$level}_decision";
        try {
            $result = DB::select("
    SELECT
        n.*,
        c.name as category_name, c.icon as category_icon, c.color as category_color,
        nominator.full_name as nominator_name, nominator.employee_code as nominator_code,
        nominee.full_name as nominee_name, nominee.employee_code as nominee_code,
        nominee.department as nominee_department, nominee.position as nominee_position
    FROM nominations n
    JOIN nomination_categories c ON n.category_id = c.id
    JOIN employees nominator ON n.nominator_id = nominator.id
    JOIN employees nominee ON n.nominee_id = nominee.id
    WHERE n.quarter = ? AND n.year = ?
    AND n.status != 'rejected'
    AND (n.{$decision_field} = 'pending' OR n.{$decision_field} IS NULL)
    $category_filter
    ORDER BY n.created_at DESC
", [$current_quarter, $current_year]);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not load the nominations.', $e);
        }

        $nominations = [];
        foreach ($result as $row) {
            // Get answers
            $row['answers'] = DB::select('
        SELECT q.question_text, a.answer
        FROM nomination_answers a
        JOIN nomination_questions q ON a.question_id = q.id
        WHERE a.nomination_id = ?
        ORDER BY q.sort_order
    ', [$row['id']]);

            $nominations[] = $row;
        }

        return kc_json(['success' => true, 'data' => $nominations]);
    }

    /** rewards/api/get_committee_stats */
    public function getCommitteeStats(Request $request)
    {
        $level = $this->committeeLevel($request, $request->query('level') ?? 1);
        $category_id = $request->query('category') !== null ? (int) $request->query('category') : 0;

        // Get current quarter/year
        [$current_quarter, $current_year] = $this->settings();

        $category_filter = $category_id > 0 ? "AND n.category_id = $category_id" : '';

        // Get stats based on level
        $decision_field = "level{$level}_decision";
        try {
            $stats = DB::selectOne("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN n.{$decision_field} = 'pending' OR n.{$decision_field} IS NULL THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN n.{$decision_field} IN ('shortlisted', 'approved') THEN 1 ELSE 0 END) as shortlisted,
        SUM(CASE WHEN n.{$decision_field} = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM nominations n
    WHERE n.quarter = ? AND n.year = ?
    AND n.status != 'rejected'
    $category_filter
", [$current_quarter, $current_year]);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not load the statistics.', $e);
        }

        return kc_json(['success' => true, 'data' => $stats]);
    }

    /** rewards/api/get_employees */
    public function getEmployees(Request $request)
    {
        $search = $request->query('search') !== null ? trim($request->query('search')) : '';
        $id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $current_user_id = $request->query('exclude_current') !== null ? (int) $request->query('exclude_current') : 0;
        $page = max(1, $request->query('page') !== null ? (int) $request->query('page') : 1);
        $limit = min(200, max(1, $request->query('limit') !== null ? (int) $request->query('limit') : 50));
        $offset = ($page - 1) * $limit;

        if ($id > 0) {
            // Single employee lookup
            $employee = DB::selectOne('SELECT id, employee_code, full_name, email, department, position as job_title, photo FROM employees WHERE id = ? AND status = 1', [$id]);

            if ($employee) {
                return kc_json(['success' => true, 'data' => $employee]);
            }

            return kc_json(['success' => false, 'message' => 'Employee not found']);
        }

        try {
            if (!empty($search)) {
                // Search employees with pagination
                $like = '%'.$search.'%';
                $where = 'WHERE status = 1
                    AND (employee_code LIKE ?
                         OR full_name LIKE ?
                         OR email LIKE ?
                         OR department LIKE ?)';
                $bindings = [$like, $like, $like, $like];
            } else {
                // Return ALL employees with pagination
                $where = 'WHERE status = 1';
                $bindings = [];
            }
            $count = DB::selectOne("SELECT COUNT(*) as total FROM employees $where", $bindings);
            $total = $count ? $count['total'] : 0;

            $result = DB::select("SELECT id, employee_code, full_name, email, department, position as job_title, photo
              FROM employees
              $where
              ORDER BY full_name ASC
              LIMIT $offset, $limit", $bindings);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not load employees.', $e);
        }

        $employees = [];
        foreach ($result as $row) {
            if ($current_user_id > 0 && $row['id'] == $current_user_id) {
                continue;
            }
            $employees[] = $row;
        }

        return kc_json([
            'success' => true,
            'data' => $employees,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < $total
        ]);
    }

    /** rewards/api/get_leaderboard */
    public function getLeaderboard(Request $request)
    {
        // Get current quarter/year
        [$current_quarter, $current_year] = $this->settings();

        // Count all nominations (including multiple from same person)
        $result = DB::select("
    SELECT
        cat.name as category_name,
        cat.id as category_id,
        e.id as nominee_id,
        e.full_name as nominee_name,
        e.employee_code as nominee_code,
        e.department,
        COUNT(n.id) as votes
    FROM nomination_categories cat
    LEFT JOIN nominations n ON cat.id = n.category_id
        AND n.status != 'rejected'
        AND n.quarter = ?
        AND n.year = ?
    LEFT JOIN employees e ON n.nominee_id = e.id
    WHERE cat.status = 1
    GROUP BY cat.id, n.nominee_id
    ORDER BY cat.sort_order, votes DESC
", [$current_quarter, $current_year]);

        $leaderboard = [];
        foreach ($result as $row) {
            $category = $row['category_name'];
            if (!isset($leaderboard[$category])) {
                $leaderboard[$category] = [];
            }
            if ($row['nominee_id'] && count($leaderboard[$category]) < 5) {
                $leaderboard[$category][] = $row;
            }
        }

        return kc_json(['success' => true, 'data' => $leaderboard]);
    }

    /** rewards/api/get_questions */
    public function getQuestions(Request $request)
    {
        $category_id = $request->query('category_id') !== null ? (int) $request->query('category_id') : 0;

        if (!$category_id) {
            return kc_json(['success' => false, 'message' => 'Category ID required']);
        }

        // Check if category exists
        $cat_check = DB::select('SELECT id, name FROM nomination_categories WHERE id = ? AND status = 1', [$category_id]);
        if (count($cat_check) == 0) {
            return kc_json(['success' => false, 'message' => 'Category not found']);
        }

        $questions = DB::select('SELECT id, question_text, question_type, options, is_required
          FROM nomination_questions
          WHERE category_id = ? AND status = 1
          ORDER BY sort_order', [$category_id]);

        return kc_json([
            'success' => true,
            'data' => $questions,
            'has_questions' => count($questions) > 0,
            'total_questions' => count($questions)
        ]);
    }

    /** rewards/api/get_user_nominations_history */
    public function getUserNominationsHistory(Request $request)
    {
        $user_id = session()->has('employee_id') ? (int) session('employee_id') : 0;
        $quarter = $request->query('quarter') !== null ? (int) $request->query('quarter') : 0;
        $year = $request->query('year') !== null ? (int) $request->query('year') : 0;

        if (!$user_id) {
            return kc_json(['success' => false, 'message' => 'User not logged in']);
        }

        $where = [];
        if ($quarter > 0) {
            $where[] = "n.quarter = $quarter";
        }
        if ($year > 0) {
            $where[] = "n.year = $year";
        }

        // (the old code started these filters with a second WHERE, which broke the query)
        $where_sql = !empty($where) ? 'AND '.implode(' AND ', $where) : '';

        try {
            $nominations = DB::select("
    SELECT n.*,
           cat.name as category_name, cat.color as category_color, cat.icon as category_icon,
           e.id as nominee_id, e.full_name as nominee_name, e.employee_code as nominee_code, e.department,
           n.quarter, n.year,
           CASE
               WHEN n.quarter = 1 THEN 'Q1 (Jan-Mar)'
               WHEN n.quarter = 2 THEN 'Q2 (Apr-Jun)'
               WHEN n.quarter = 3 THEN 'Q3 (Jul-Sep)'
               WHEN n.quarter = 4 THEN 'Q4 (Oct-Dec)'
           END as quarter_name
    FROM nominations n
    JOIN nomination_categories cat ON n.category_id = cat.id
    JOIN employees e ON n.nominee_id = e.id
    WHERE n.nominator_id = ?
    $where_sql
    ORDER BY n.year DESC, n.quarter DESC, n.created_at DESC
", [$user_id]);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not load your nominations.', $e);
        }

        // Get available years
        $years = [];
        foreach (DB::select('SELECT DISTINCT year FROM nominations WHERE nominator_id = ? ORDER BY year DESC', [$user_id]) as $year_row) {
            $years[] = $year_row['year'];
        }

        return kc_json([
            'success' => true,
            'data' => $nominations,
            'years' => $years,
            'current_quarter' => date('n') <= 3 ? 1 : (date('n') <= 6 ? 2 : (date('n') <= 9 ? 3 : 4))
        ]);
    }

    /** rewards/api/submit_committee_decision */
    public function submitCommitteeDecision(Request $request)
    {
        if (!$request->isMethod('post')) {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $employee_id = (int) (session('employee_id') ?? 0);
        $nomination_id = $request->post('nomination_id') !== null ? (int) $request->post('nomination_id') : 0;
        $level = $request->post('level') !== null ? (int) $request->post('level') : 1;
        $decision = $request->post('decision') !== null ? (string) $request->post('decision') : '';
        $notes = $request->post('notes') !== null ? (string) $request->post('notes') : '';

        if (!$employee_id || !$nomination_id || !$decision) {
            return kc_json(['success' => false, 'message' => 'Missing required fields']);
        }
        if (!isset(self::LEVEL_DECISIONS[$level]) || !in_array($decision, self::LEVEL_DECISIONS[$level], true)) {
            return kc_json(['success' => false, 'message' => 'Invalid decision for this level']);
        }

        // Verify committee membership for this level
        $committee_check = DB::select('
    SELECT id FROM nomination_committee
    WHERE employee_id = ? AND committee_level = ? AND status = 1
', [$employee_id, $level]);
        if (count($committee_check) == 0) {
            return kc_json(['success' => false, 'message' => 'You are not authorized for this level']);
        }

        $nom = DB::selectOne('SELECT id, category_id, quarter, year, nominee_id FROM nominations WHERE id = ?', [$nomination_id]);
        if (!$nom) {
            return kc_json(['success' => false, 'message' => 'Nomination not found']);
        }

        // Update nomination with decision (level is 1-3, checked above)
        $decision_field = "level{$level}_decision";
        $reviewed_by_field = "level{$level}_reviewed_by";
        $reviewed_at_field = "level{$level}_reviewed_at";
        $notes_field = "level{$level}_notes";

        try {
            DB::transaction(function () use ($decision_field, $reviewed_by_field, $reviewed_at_field, $notes_field, $decision, $employee_id, $notes, $nomination_id, $level, $nom) {
                DB::update("
    UPDATE nominations
    SET {$decision_field} = ?,
        {$reviewed_by_field} = ?,
        {$reviewed_at_field} = NOW(),
        {$notes_field} = ?
    WHERE id = ?
", [$decision, $employee_id, $notes, $nomination_id]);

                if ($level == 3 && $decision == 'approved') {
                    // Level 3 approval: final status, and this nominee becomes the category winner for the quarter
                    DB::update("
            UPDATE nominations
            SET status = 'approved', is_winner = 1, winner_announced_at = NOW()
            WHERE id = ?
        ", [$nomination_id]);
                    $this->recordCategoryWinner($nom);
                } elseif ($level == 3 && $decision == 'rejected') {
                    DB::update("UPDATE nominations SET status = 'rejected' WHERE id = ?", [$nomination_id]);
                }
            });
        } catch (QueryException $e) {
            NominationsLegacy::failJson('The decision could not be saved (database error). Please try again.', $e);
        }

        return kc_json(['success' => true, 'message' => 'Decision recorded successfully']);
    }

    /**
     * Record the Level-3 approved nomination as the quarter's category winner.
     * The schema keeps winners in quarter_winners (rank 1 = winner, as manage-quarters does); the old
     * code wrote nomination_categories.quarter_winner_* columns that the shipped schema does not have,
     * so those are only updated when a database actually has them.
     */
    private function recordCategoryWinner(array $nom): void
    {
        if ($nom['quarter'] !== null && $nom['year'] !== null) {
            // the previous rank-1 winner of this category/quarter (if another nomination) is replaced
            $previous = DB::select('SELECT nomination_id FROM quarter_winners WHERE category_id = ? AND quarter = ? AND year = ? AND winner_rank = 1', [$nom['category_id'], $nom['quarter'], $nom['year']]);
            foreach ($previous as $prev) {
                if ((int) $prev['nomination_id'] !== (int) $nom['id']) {
                    DB::update('UPDATE nominations SET is_winner = 0 WHERE id = ?', [$prev['nomination_id']]);
                }
            }
            DB::delete('DELETE FROM quarter_winners WHERE category_id = ? AND quarter = ? AND year = ? AND (winner_rank = 1 OR nomination_id = ?)', [$nom['category_id'], $nom['quarter'], $nom['year'], $nom['id']]);
            DB::insert('INSERT INTO quarter_winners (category_id, nomination_id, nominee_id, quarter, year, winner_rank, announced_at) VALUES (?, ?, ?, ?, ?, 1, NOW())', [$nom['category_id'], $nom['id'], $nom['nominee_id'], $nom['quarter'], $nom['year']]);
        }

        $legacy = ['quarter_winner_id' => $nom['nominee_id'], 'quarter_winner_quarter' => $nom['quarter'], 'quarter_winner_year' => $nom['year']];
        $set = array_filter($legacy, fn ($column) => $this->categoryHasColumn($column), ARRAY_FILTER_USE_KEY);
        if ($set) {
            DB::table('nomination_categories')->where('id', $nom['category_id'])->update($set);
        }
    }

    /** rewards/api/submit_nomination */
    public function submitNomination(Request $request)
    {
        if (!$request->isMethod('post')) {
            return kc_json(['success' => false, 'message' => 'Invalid request method']);
        }

        $category_id = $request->post('category_id') !== null ? (int) $request->post('category_id') : 0;
        $nominator_id = $request->post('nominator_id') !== null ? (int) $request->post('nominator_id') : 0;
        $nominee_id = $request->post('nominee_id') !== null ? (int) $request->post('nominee_id') : 0;
        $answers_json = $request->post('answers') !== null ? (string) $request->post('answers') : '[]';

        // The nominator is the logged-in employee (the form posts their id; it may not be someone else's)
        $session_employee_id = (int) (session('employee_id') ?? 0);
        if (!$session_employee_id) {
            return kc_json(['success' => false, 'message' => 'Please log in to submit a nomination'], 401);
        }
        if ($nominator_id && $nominator_id !== $session_employee_id) {
            return kc_json(['success' => false, 'message' => 'You can only submit nominations as yourself'], 403);
        }
        $nominator_id = $session_employee_id;

        if (!$category_id || !$nominator_id || !$nominee_id) {
            return kc_json(['success' => false, 'message' => 'Missing required fields']);
        }

        if (!DB::selectOne('SELECT id FROM employees WHERE id = ? AND status = 1', [$nominee_id])) {
            return kc_json(['success' => false, 'message' => 'The selected nominee was not found']);
        }

        // Check if category exists and is active
        $category_data = DB::selectOne('SELECT id, name, is_self_nomination FROM nomination_categories WHERE id = ? AND status = 1', [$category_id]);
        if (!$category_data) {
            return kc_json(['success' => false, 'message' => 'Invalid or inactive category']);
        }

        // For self-nomination categories, nominee must be the nominator
        if ($category_data['is_self_nomination'] == 1) {
            if ($nominator_id != $nominee_id) {
                return kc_json(['success' => false, 'message' => 'This category only allows self-nominations']);
            }
        } else {
            if ($nominator_id == $nominee_id) {
                return kc_json(['success' => false, 'message' => 'You cannot nominate yourself for this category']);
            }
        }

        // Get current quarter settings
        [$current_quarter, $current_year, $unique_votes_enabled] = $this->settings(true);

        // Check unique vote restriction if enabled
        if ($unique_votes_enabled == 1) {
            $existing_vote = DB::select('
        SELECT id FROM nomination_user_tracking
        WHERE user_id = ?
        AND category_id = ?
        AND quarter = ?
        AND year = ?
    ', [$nominator_id, $category_id, $current_quarter, $current_year]);

            if (count($existing_vote) > 0) {
                return kc_json([
                    'success' => false,
                    'message' => 'You have already submitted a nomination in this category for the current quarter. Only one nomination per category per quarter is allowed.'
                ]);
            }
        }

        // Get questions for this category to know which are attachment types
        $attachment_questions = [];
        foreach (DB::select('SELECT id, question_type FROM nomination_questions WHERE category_id = ? AND status = 1', [$category_id]) as $q) {
            if ($q['question_type'] == 'attachment') {
                $attachment_questions[] = $q['id'];
            }
        }

        // Validate the answers: a list of {question_id, answer} for this category's questions
        $decoded = json_decode($answers_json, true);
        $category_question_ids = array_map('intval', array_column(DB::select('SELECT id FROM nomination_questions WHERE category_id = ? AND status = 1', [$category_id]), 'id'));
        $answers = [];
        foreach (is_array($decoded) ? $decoded : [] as $answer) {
            if (!is_array($answer) || !isset($answer['question_id'])) {
                return kc_json(['success' => false, 'message' => 'Invalid answers submitted']);
            }
            $value = $answer['answer'] ?? '';
            if (is_array($value)) {
                $value = implode(', ', array_filter($value, 'is_scalar'));
            } elseif (!is_scalar($value)) {
                return kc_json(['success' => false, 'message' => 'Invalid answers submitted']);
            }
            $question_id = (int) $answer['question_id'];
            if (in_array($question_id, $category_question_ids, true)) {
                $answers[] = ['question_id' => $question_id, 'answer' => trim((string) $value)];
            }
        }
        if (count($answers) == 0) {
            return kc_json(['success' => false, 'message' => 'Please answer all required questions']);
        }

        // Get required questions for this category
        $required_ids = [];
        foreach (DB::select('SELECT id, question_text, question_type FROM nomination_questions WHERE category_id = ? AND is_required = 1 AND status = 1', [$category_id]) as $rq) {
            $required_ids[(int) $rq['id']] = $rq['question_type'];
        }

        // Check if all required questions have (non-empty) answers
        $answered_question_ids = array_column(array_filter($answers, fn ($a) => $a['answer'] !== ''), 'question_id');
        $missing_questions = array_diff(array_keys($required_ids), $answered_question_ids);

        if (!empty($missing_questions)) {
            return kc_json(['success' => false, 'message' => 'Please answer all required questions before submitting']);
        }

        // Attachments: the form posts them as "attachment_<question id>" (older forms: attachments[attachment_<id>])
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip'];
        $attachment_files = [];
        $legacy_files = $request->file('attachments');
        foreach ($attachment_questions as $qid) {
            $key = 'attachment_'.$qid;
            $file = $request->file($key) ?? (is_array($legacy_files) ? ($legacy_files[$key] ?? null) : null);
            if (!$file) {
                continue;
            }
            if (!$file->isValid()) {
                return kc_json(['success' => false, 'message' => 'An attachment could not be uploaded. Please try again.']);
            }
            if (!in_array(strtolower($file->getClientOriginalExtension()), $allowed_extensions, true)) {
                return kc_json(['success' => false, 'message' => 'Attachment type not allowed. Allowed: '.implode(', ', $allowed_extensions)]);
            }
            $attachment_files[(int) $qid] = $file;
        }

        // Create upload directory if it doesn't exist
        $upload_dir = kc_upload_path('nomination_attachments/');
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Get nominator and nominee details for email
        $nominator = DB::selectOne('SELECT id, full_name, email, employee_code FROM employees WHERE id = ?', [$nominator_id]);
        $nominee = DB::selectOne('SELECT id, full_name, email, employee_code, department FROM employees WHERE id = ?', [$nominee_id]);

        // Insert nomination, tracking row and answers together
        try {
            $nomination_id = DB::transaction(function () use ($category_id, $nominator_id, $nominee_id, $current_quarter, $current_year, $unique_votes_enabled, $answers, $attachment_files, $upload_dir) {
                DB::insert("INSERT INTO nominations (category_id, nominator_id, nominee_id, status, is_read, quarter, year)
          VALUES (?, ?, ?, 'pending', 0, ?, ?)", [$category_id, $nominator_id, $nominee_id, (int) $current_quarter, (int) $current_year]);
                $nomination_id = (int) DB::getPdo()->lastInsertId();

                // Record in tracking table if unique votes enabled
                if ($unique_votes_enabled == 1) {
                    DB::insert('
            INSERT INTO nomination_user_tracking (user_id, category_id, quarter, year, nomination_id)
            VALUES (?, ?, ?, ?, ?)
        ', [$nominator_id, $category_id, $current_quarter, $current_year, $nomination_id]);
                }

                foreach ($answers as $answer) {
                    $question_id = $answer['question_id'];
                    $answer_text = $answer['answer'];

                    // Store the uploaded file for attachment questions
                    if (isset($attachment_files[$question_id])) {
                        $file = $attachment_files[$question_id];
                        $filename = time().'_'.$nomination_id.'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                        $file->move($upload_dir, $filename);
                        $answer_text = 'uploads/nomination_attachments/'.$filename;
                    }

                    if ($answer_text !== '') {
                        DB::insert('INSERT INTO nomination_answers (nomination_id, question_id, answer) VALUES (?, ?, ?)', [$nomination_id, $question_id, $answer_text]);
                    }
                }

                return $nomination_id;
            });
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Your nomination could not be saved (database error). Please try again.', $e);
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
            NominationsLegacy::failJson('An attachment could not be saved. Please try again.', $e);
        }

        // ─── SEND THANK YOU EMAIL TO NOMINATOR ─────────────────────────────
        $emailSent = false;
        if (!empty($nominator['email'])) {
            $emailSent = sendNominationThankYouEmail(
                $nominator['email'],
                $nominator['full_name'],
                $category_data['name'],
                $nominee['full_name'] ?? null,
                $current_quarter,
                $current_year,
                $nomination_id
            );
        }

        // ─── SEND NOTIFICATION TO COMMITTEE (Optional) ─────────────────────
        // Get committee members for Level 1
        $committeeEmails = [];
        foreach (DB::select('
        SELECT e.id, e.full_name, e.email
        FROM nomination_committee nc
        JOIN employees e ON nc.employee_id = e.id
        WHERE nc.committee_level = 1 AND nc.status = 1
        ORDER BY nc.sort_order ASC
        LIMIT 3
    ') as $member) {
            if (!empty($member['email'])) {
                $committeeEmails[] = $member;
            }
        }

        // Send notification to committee members
        foreach ($committeeEmails as $member) {
            sendNominationNotificationToCommittee(
                $member['email'],
                $member['full_name'],
                $nominator['full_name'] ?? null,
                $nominee['full_name'] ?? null,
                $category_data['name'],
                $current_quarter,
                $current_year,
                $nomination_id
            );
        }

        return kc_json([
            'success' => true,
            'message' => 'Nomination submitted successfully',
            'quarter' => $current_quarter,
            'year' => $current_year,
            'unique_votes_enabled' => $unique_votes_enabled == 1,
            'email_sent' => $emailSent
        ]);
    }
}
