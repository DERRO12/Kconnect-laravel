<?php

namespace App\Http\Controllers\Rewards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Employee side of the quarterly awards nominations (rewards/ folder). */
class NominationController extends Controller
{
    /** Current employee row (old: SELECT ... WHERE id = $employee_id AND status = 1) */
    private function currentEmployee($employee_id)
    {
        return DB::selectOne('SELECT id, employee_code, full_name, email, department, position, photo FROM employees WHERE id = ? AND status = 1', [$employee_id]);
    }

    /** rewards/nomination */
    public function nomination(Request $request)
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            session(['redirect_after_login' => kc_request_uri()]);
            kc_redirect('../login');
        }

        // Get current employee data
        $currentEmployee = [];
        $employee_id = session('employee_id') ?? 0;
        if ($employee_id) {
            $currentEmployee = $this->currentEmployee($employee_id);
        }

        if (!$currentEmployee) {
            kc_redirect('../login');
        }
        // Get current quarter settings for display
        $current_quarter_info = [];
        $settings = DB::selectOne('SELECT current_quarter, current_year FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');
        $quarter_names = [
            1 => 'Q1 (January - March)',
            2 => 'Q2 (April - June)',
            3 => 'Q3 (July - September)',
            4 => 'Q4 (October - December)'
        ];
        if ($settings) {
            $current_quarter_num = $settings['current_quarter'];
            $current_year = $settings['current_year'];

            $current_quarter_info = [
                'quarter_num' => $current_quarter_num,
                'quarter_name' => $quarter_names[$current_quarter_num] ?? null,
                'year' => $current_year,
                'display' => ($quarter_names[$current_quarter_num] ?? null) . ' ' . $current_year
            ];
        } else {
            // Fallback to current date
            $current_month = date('n');
            $current_quarter_num = ceil($current_month / 3);
            $current_quarter_info = [
                'quarter_num' => $current_quarter_num,
                'quarter_name' => $quarter_names[$current_quarter_num],
                'year' => date('Y'),
                'display' => $quarter_names[$current_quarter_num] . ' ' . date('Y')
            ];
        }

        return view('rewards.nomination', compact('currentEmployee', 'current_quarter_info'));
    }

    /** rewards/committee-members */
    public function committeeMembers(Request $request)
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            session(['redirect_after_login' => kc_request_uri()]);
            kc_redirect('../login');
        }

        // Get current employee data
        $currentEmployee = [];
        $employee_id = session('employee_id') ?? 0;
        if ($employee_id) {
            $currentEmployee = $this->currentEmployee($employee_id);
        }

        if (!$currentEmployee) {
            kc_redirect('../login');
        }

        // Get committee members by level
        $levelQuery = "
    SELECT
        c.committee_level, c.role,
        e.id, e.employee_code, e.full_name, e.department, e.position, e.photo, e.email
    FROM nomination_committee c
    JOIN employees e ON c.employee_id = e.id
    WHERE c.committee_level = ? AND c.status = 1
    ORDER BY
        CASE c.role
            WHEN 'Chair' THEN 1
            WHEN 'Co-Chair' THEN 2
            WHEN 'Secretary' THEN 3
            ELSE 4
        END,
        c.sort_order,
        e.full_name
";

        $level1_members = DB::select($levelQuery, [1]);
        $level2_members = DB::select($levelQuery, [2]);
        $level3_members = DB::select($levelQuery, [3]);

        // Get current quarter/year for display
        $settings = DB::selectOne('SELECT current_quarter, current_year FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');
        if ($settings) {
            $current_quarter = $settings['current_quarter'];
            $current_year = $settings['current_year'];
        } else {
            $current_quarter = ceil(date('n') / 3);
            $current_year = date('Y');
        }

        $quarter_names = [
            1 => 'Q1 (January - March)',
            2 => 'Q2 (April - June)',
            3 => 'Q3 (July - September)',
            4 => 'Q4 (October - December)'
        ];

        return view('rewards.committee-members', compact('currentEmployee', 'level1_members', 'level2_members', 'level3_members', 'current_quarter', 'current_year', 'quarter_names'));
    }

    /** rewards/committee-review */
    public function committeeReview(Request $request)
    {
        // Check if user is a committee member
        $employee_id = session('employee_id') ?? 0;
        if (!$employee_id) {
            kc_redirect('../login');
        }

        // Check committee membership and level
        $rows = DB::select('
    SELECT committee_level, role FROM nomination_committee
    WHERE employee_id = ? AND status = 1
', [$employee_id]);
        $committee_memberships = [];
        foreach ($rows as $row) {
            $committee_memberships[$row['committee_level']] = $row['role'];
        }

        if (empty($committee_memberships)) {
            // Not a committee member - redirect or show access denied
            abort(403, 'Access Denied - You are not a committee member');
        }

        $currentEmployee = $this->currentEmployee($employee_id);

        // Get current quarter/year
        $settings = DB::selectOne('SELECT current_quarter, current_year FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');
        if ($settings) {
            $current_quarter = $settings['current_quarter'];
            $current_year = $settings['current_year'];
        } else {
            $current_quarter = ceil(date('n') / 3);
            $current_year = date('Y');
        }

        $selected_level = $request->query('level') !== null ? (int) $request->query('level') : min(array_keys($committee_memberships));
        if (!isset($committee_memberships[$selected_level])) {
            // only the levels this employee sits on can be reviewed
            $selected_level = min(array_keys($committee_memberships));
        }
        $selected_category = $request->query('category') !== null ? (int) $request->query('category') : 0;

        return view('rewards.committee-review', compact('committee_memberships', 'currentEmployee', 'current_quarter', 'current_year', 'selected_level', 'selected_category'));
    }
}
