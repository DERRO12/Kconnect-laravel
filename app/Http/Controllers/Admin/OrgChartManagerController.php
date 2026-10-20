<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\OrgChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Organization chart manager (admin/orgchart_manager): add, edit, move, remove and bulk add
 * people in the reporting structure (table orgchart_structure), plus structure checks.
 * POST actions follow Post / Redirect / Get; CSRF is checked by Laravel (@csrf on each form).
 */
class OrgChartManagerController extends Controller
{
    private const TABS = ['structure', 'table', 'pending'];

    /** admin/orgchart_manager */
    public function index(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect(url('admin/login'));
        }

        if ($request->isMethod('post') && $request->has('action')) {
            try {
                $tab = $this->handleAction($request);
            } catch (\Throwable $e) {
                report($e);
                $this->flash('Something went wrong: '.$e->getMessage(), 'danger');
                $tab = '';
            }

            return kc_redirect_response(url('admin/orgchart_manager').($tab ? '?tab='.urlencode($tab) : ''));
        }

        $orgData = OrgChart::nodes();
        $byId = array_column($orgData, null, 'employee_id');

        $employees = [];
        foreach (DB::select('SELECT e.id, e.employee_code, e.full_name, e.position, e.department, e.photo, c.name AS company_name
                FROM employees e LEFT JOIN companies c ON c.id = e.company_id
                WHERE e.status = 1 ORDER BY e.full_name') as $r) {
            $employees[] = [
                'id' => (int) $r['id'],
                'employee_code' => (string) $r['employee_code'],
                'full_name' => (string) $r['full_name'],
                'position' => (string) ($r['position'] ?? ''),
                'department' => (string) ($r['department'] ?? ''),
                'company_name' => (string) ($r['company_name'] ?? ''),
                'photo' => OrgChart::photoUrl($r['photo']),
                'in_chart' => isset($byId[(int) $r['id']]),
            ];
        }
        $pending = array_values(array_filter($employees, fn ($e) => ! $e['in_chart']));

        // Structure checks (rendered and escaped in the view)
        $issues = [];
        foreach (DB::select('SELECT e.id, e.full_name FROM orgchart_structure os JOIN employees e ON e.id = os.employee_id
                WHERE e.status = 0 OR os.status = 0') as $r) {
            $issues[] = ['type' => 'inactive', 'id' => (int) $r['id'], 'name' => (string) $r['full_name']];
        }
        foreach ($orgData as $n) {
            if (! empty($n['orphan_of'])) {
                $issues[] = ['type' => 'orphan', 'id' => $n['employee_id'], 'name' => $n['full_name']];
            } elseif ($n['parent_id'] && isset($byId[$n['parent_id']]) && $byId[$n['parent_id']]['level'] >= $n['level']) {
                $p = $byId[$n['parent_id']];
                $issues[] = ['type' => 'level', 'id' => $n['employee_id'], 'name' => $n['full_name'], 'label' => $n['level_label'],
                    'manager' => $p['full_name'], 'manager_label' => $p['level_label']];
            }
        }

        $totalInChart = count($orgData);
        $topLevelCount = count(array_filter($orgData, fn ($e) => $e['parent_id'] === null));
        $levelsUsed = count(array_unique(array_column($orgData, 'level')));
        $activeTab = in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'structure';
        $levels = OrgChart::LEVELS;

        $message = session()->pull('message');
        $messageType = session()->pull('message_type');

        return view('admin.orgchart_manager', compact(
            'orgData', 'employees', 'pending', 'issues', 'totalInChart', 'topLevelCount', 'levelsUsed',
            'activeTab', 'levels', 'message', 'messageType'
        ));
    }

    /** Runs one POST action and returns the tab to go back to. */
    private function handleAction(Request $request): string
    {
        $tab = preg_replace('/[^a-z]/', '', (string) $request->post('tab', ''));

        return match ($request->post('action')) {
            'save' => $this->save($request, $tab),
            'bulk_add' => $this->bulkAdd($request),
            'delete' => $this->remove($request, $tab),
            'move' => $this->move($request, $tab),
            default => '',
        };
    }

    /** Add a person to the chart, or update their manager / level / title / order. */
    private function save(Request $request, string $tab): string
    {
        $employeeId = (int) $request->post('employee_id', 0);
        $parentId = ! empty($request->post('parent_id')) ? (int) $request->post('parent_id') : null;
        $level = OrgChart::level($request->post('level', 8));
        $levelLabel = trim((string) $request->post('level_label', ''));
        $levelLabel = $levelLabel === '' || $levelLabel === OrgChart::levelLabel($level) ? null : mb_substr($levelLabel, 0, 50);
        $sortOrder = max(0, min(9999, (int) $request->post('sort_order', 0)));

        if ($employeeId <= 0) {
            return $this->fail('Please choose an employee.', $tab);
        }
        $employee = DB::selectOne('SELECT full_name FROM employees WHERE id = ? AND status = 1', [$employeeId]);
        if (! $employee) {
            return $this->fail('That employee was not found or is inactive.', $tab);
        }
        $name = (string) $employee['full_name'];
        if ($err = OrgChart::checkParent($employeeId, $parentId)) {
            return $this->fail($err, $tab);
        }

        if (DB::selectOne('SELECT id FROM orgchart_structure WHERE employee_id = ?', [$employeeId])) {
            DB::update('UPDATE orgchart_structure SET parent_id = ?, level = ?, level_label = ?, sort_order = ?, status = 1, updated_at = NOW() WHERE employee_id = ?',
                [$parentId, $level, $levelLabel, $sortOrder, $employeeId]);
            $this->flash("$name's position in the chart was updated.");
        } else {
            DB::insert('INSERT INTO orgchart_structure (employee_id, parent_id, level, level_label, sort_order, status) VALUES (?, ?, ?, ?, ?, 1)',
                [$employeeId, $parentId, $level, $levelLabel, $sortOrder]);
            $this->flash("$name was added to the organization chart.");
        }

        return $tab;
    }

    /** Add several people under the same manager at one level. */
    private function bulkAdd(Request $request): string
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $request->post('employee_ids', [])))));
        $parentId = ! empty($request->post('parent_id')) ? (int) $request->post('parent_id') : null;
        $level = OrgChart::level($request->post('level', 8));

        if (! $ids) {
            return $this->fail('Tick at least one employee to add.', 'pending');
        }
        if ($err = OrgChart::checkParent(0, $parentId)) {
            return $this->fail($err, 'pending');
        }

        $added = 0;
        foreach ($ids as $id) {
            if ($id === $parentId) {
                continue;
            }
            $rows = DB::affectingStatement('INSERT INTO orgchart_structure (employee_id, parent_id, level, sort_order, status)
                SELECT e.id, ?, ?, 0, 1 FROM employees e
                WHERE e.id = ? AND e.status = 1 AND NOT EXISTS (SELECT 1 FROM orgchart_structure x WHERE x.employee_id = e.id)',
                [$parentId, $level, $id]);
            if (! $rows) {
                // An active employee whose chart entry is switched off (status 0) is listed as "not in chart":
                // switch that entry back on under the chosen manager instead of silently skipping them.
                $rows = DB::update('UPDATE orgchart_structure os JOIN employees e ON e.id = os.employee_id
                    SET os.parent_id = ?, os.level = ?, os.status = 1, os.updated_at = NOW()
                    WHERE os.employee_id = ? AND os.status = 0 AND e.status = 1', [$parentId, $level, $id]);
            }
            $added += $rows > 0 ? 1 : 0;
        }
        $this->flash("$added employee".($added === 1 ? '' : 's').' added to the organization chart.');

        return 'pending';
    }

    /** Take someone out of the chart: keep their team (it moves one level up) or remove the whole branch. */
    private function remove(Request $request, string $tab): string
    {
        $employeeId = (int) $request->post('employee_id', 0);
        $mode = $request->post('mode', 'reassign') === 'branch' ? 'branch' : 'reassign';
        $row = DB::selectOne('SELECT parent_id FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);
        $parentId = $row && $row['parent_id'] !== null ? (int) $row['parent_id'] : null;
        $name = (string) (DB::selectOne('SELECT full_name FROM employees WHERE id = ?', [$employeeId])['full_name'] ?? '');

        try {
            $msg = DB::transaction(function () use ($mode, $employeeId, $parentId, $name) {
                if ($mode === 'branch') {
                    $ids = array_merge([$employeeId], OrgChart::descendantIds($employeeId));
                    DB::delete('DELETE FROM orgchart_structure WHERE employee_id IN ('.implode(',', array_fill(0, count($ids), '?')).')', $ids);

                    return count($ids) > 1
                        ? "$name and ".(count($ids) - 1).' people below them were removed from the chart.'
                        : "$name was removed from the chart.";
                }
                $moved = DB::update('UPDATE orgchart_structure SET parent_id = ? WHERE parent_id = ?', [$parentId, $employeeId]);
                DB::delete('DELETE FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);

                return "$name was removed from the chart."
                    .($moved > 0 ? " Their $moved reportee".($moved === 1 ? '' : 's').' now report one level up.' : '');
            });
            $this->flash($msg);
        } catch (\Throwable $e) {
            report($e);
            $this->flash('Could not remove: '.$e->getMessage(), 'danger');
        }

        return $tab;
    }

    /** Move someone one place up or down among the people who share their manager. */
    private function move(Request $request, string $tab): string
    {
        $employeeId = (int) $request->post('employee_id', 0);
        $dir = $request->post('dir') === 'up' ? -1 : 1;
        $row = DB::selectOne('SELECT parent_id FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);
        $parentId = $row && $row['parent_id'] !== null ? (int) $row['parent_id'] : null;

        $siblings = array_map('intval', array_column(DB::select(
            'SELECT os.employee_id FROM orgchart_structure os JOIN employees e ON e.id = os.employee_id
             WHERE os.status = 1 AND e.status = 1 AND '.($parentId === null ? 'os.parent_id IS NULL' : 'os.parent_id = ?').'
             ORDER BY os.sort_order, os.level, e.full_name',
            $parentId === null ? [] : [$parentId]
        ), 'employee_id'));

        $i = array_search($employeeId, $siblings, true);
        $j = $i === false ? false : $i + $dir;
        if ($j !== false && $j >= 0 && $j < count($siblings)) {
            [$siblings[$i], $siblings[$j]] = [$siblings[$j], $siblings[$i]];
            DB::transaction(function () use ($siblings) {
                foreach ($siblings as $pos => $id) {
                    DB::update('UPDATE orgchart_structure SET sort_order = ? WHERE employee_id = ?', [($pos + 1) * 10, $id]);
                }
            });
        }

        return $tab;
    }

    private function flash(string $message, string $type = 'success'): void
    {
        session(['message' => $message, 'message_type' => $type]);
    }

    private function fail(string $message, string $tab): string
    {
        $this->flash($message, 'danger');

        return $tab;
    }
}
