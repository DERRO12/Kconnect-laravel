<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Organization chart shared code (was orgchart_lib.php).
 * Used by orgchart_view, api/get_orgchart and admin/orgchart_manager.
 *
 * To change level names or colours, edit LEVELS below: the chart, the legend
 * and the admin forms all read from it.
 */
final class OrgChart
{
    public const LEVELS = [
        1 => ['label' => 'CEO / C-Suite',  'short' => 'CEO',         'color' => '#76bc21'],
        2 => ['label' => 'Director',       'short' => 'Director',    'color' => '#f59e0b'],
        3 => ['label' => 'Senior Head',    'short' => 'Sr. Head',    'color' => '#8b5cf6'],
        4 => ['label' => 'Head',           'short' => 'Head',        'color' => '#3b82f6'],
        5 => ['label' => 'Senior Manager', 'short' => 'Sr. Manager', 'color' => '#06b6d4'],
        6 => ['label' => 'Manager',        'short' => 'Manager',     'color' => '#14b8a6'],
        7 => ['label' => 'Supervisor',     'short' => 'Supervisor',  'color' => '#6366f1'],
        8 => ['label' => 'Staff',          'short' => 'Staff',       'color' => '#64748b'],
    ];

    /** Folder (under public/) where employee photos are stored. */
    public const PHOTO_DIR = 'uploads/employees/';

    /** json_encode flags for data embedded in a <script> block. */
    public const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

    /** Clamp any number to a valid level (1-8). */
    public static function level($level): int
    {
        return max(1, min(8, (int) $level));
    }

    /** Default label of a level, e.g. 4 => "Head". */
    public static function levelLabel(int $level): string
    {
        return self::LEVELS[self::level($level)]['label'];
    }

    /**
     * URL of the value stored in employees.photo, built with asset() so it works in a subfolder.
     * Handles plain file names ("abc.jpg"), stored paths ("uploads/employees/abc.jpg") and full URLs.
     */
    public static function photoUrl(?string $photo): string
    {
        $photo = trim((string) $photo);
        if ($photo === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $photo)) {
            return $photo;
        }
        $photo = ltrim(str_replace('\\', '/', $photo), '/');

        return str_contains($photo, '/') ? kc_asset($photo) : kc_upload_url($photo, self::PHOTO_DIR);
    }

    /**
     * All active org chart entries (active employees only), ordered for display,
     * keyed by the names the front end expects.
     */
    public static function nodes(): array
    {
        $result = DB::select('
            SELECT os.id AS org_id, os.employee_id, os.parent_id, os.level, os.level_label, os.sort_order,
                   e.employee_code, e.full_name, e.email, e.phone, e.extension, e.mobile,
                   e.photo, e.department, e.position, e.join_date,
                   c.id AS company_id, c.name AS company_name
            FROM orgchart_structure os
            INNER JOIN employees e ON e.id = os.employee_id
            LEFT JOIN companies c ON c.id = e.company_id
            WHERE os.status = 1 AND e.status = 1
            ORDER BY os.level ASC, os.sort_order ASC, e.full_name ASC');

        $rows = [];
        foreach ($result as $r) {
            $level = self::level($r['level']);
            $rows[] = [
                'org_id' => (int) $r['org_id'],
                'employee_id' => (int) $r['employee_id'],
                'parent_id' => $r['parent_id'] !== null ? (int) $r['parent_id'] : null,
                'level' => $level,
                'level_label' => ($r['level_label'] ?? '') !== '' ? $r['level_label'] : self::LEVELS[$level]['label'],
                'sort_order' => (int) $r['sort_order'],
                'employee_code' => (string) $r['employee_code'],
                'full_name' => (string) $r['full_name'],
                'position' => (string) ($r['position'] ?? ''),
                'department' => (string) ($r['department'] ?? ''),
                'company_id' => $r['company_id'] !== null ? (int) $r['company_id'] : null,
                'company_name' => (string) ($r['company_name'] ?? ''),
                'email' => (string) ($r['email'] ?? ''),
                'phone' => (string) ($r['phone'] ?? ''),
                'extension' => (string) ($r['extension'] ?? ''),
                'mobile' => (string) ($r['mobile'] ?? ''),
                'join_date' => (string) ($r['join_date'] ?? ''),
                'photo' => self::photoUrl($r['photo']),
            ];
        }

        // A manager who left (inactive) or was removed would leave reportees pointing
        // at nobody. Show those reportees as top level instead of hiding them.
        $ids = array_flip(array_column($rows, 'employee_id'));
        foreach ($rows as &$row) {
            if ($row['parent_id'] !== null && ! isset($ids[$row['parent_id']])) {
                $row['orphan_of'] = $row['parent_id'];
                $row['parent_id'] = null;
            }
        }
        unset($row);

        return $rows;
    }

    /**
     * Employee ids of everyone below $employeeId (direct and indirect reportees).
     * Reads the raw table so it also sees entries the chart is not showing. Safe on looped data.
     */
    public static function descendantIds(int $employeeId): array
    {
        $children = [];
        foreach (DB::select('SELECT employee_id, parent_id FROM orgchart_structure WHERE parent_id IS NOT NULL') as $r) {
            $children[(int) $r['parent_id']][] = (int) $r['employee_id'];
        }
        $found = [];
        $stack = [$employeeId];
        while ($stack) {
            $id = array_pop($stack);
            foreach ($children[$id] ?? [] as $child) {
                if (! isset($found[$child]) && $child !== $employeeId) {
                    $found[$child] = true;
                    $stack[] = $child;
                }
            }
        }

        return array_keys($found);
    }

    /**
     * Validates a manager choice for $employeeId (0 = a new person with no reportees yet).
     * Reporting-loop protection: the manager may not be the person themselves or anyone
     * who (directly or indirectly) reports to them. Returns an error message, or null when fine.
     */
    public static function checkParent(int $employeeId, ?int $parentId): ?string
    {
        if ($parentId === null) {
            return null;
        }
        if ($parentId === $employeeId) {
            return 'A person cannot report to themselves.';
        }
        $inChart = DB::selectOne('SELECT COUNT(*) AS c FROM orgchart_structure os JOIN employees e ON e.id = os.employee_id
             WHERE os.employee_id = ? AND os.status = 1 AND e.status = 1', [$parentId]);
        if (! (int) $inChart['c']) {
            return 'The selected manager is not an active member of the org chart.';
        }
        if ($employeeId > 0 && in_array($parentId, self::descendantIds($employeeId), true)) {
            return 'That manager currently reports (directly or indirectly) to this person, which would create a loop.';
        }

        return null;
    }
}
