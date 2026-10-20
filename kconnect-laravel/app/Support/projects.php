<?php

/*
|--------------------------------------------------------------------------
| Project management helpers (ported from projects-config.php)
|--------------------------------------------------------------------------
| Functions that used to return a mysqli result now return the rows as an array
| (each row an associative array, like mysqli_fetch_assoc gave).
*/

use Illuminate\Support\Facades\DB;

if (! function_exists('generateProjectCode')) {
    function generateProjectCode(): string
    {
        $year = date('Y');
        $row = DB::selectOne('SELECT COUNT(*) as count FROM projects WHERE YEAR(created_at) = ?', [$year]);
        $count = $row['count'] + 1;

        return 'PRJ-'.$year.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('generateTaskCode')) {
    function generateTaskCode($project_id): string
    {
        $project = DB::selectOne('SELECT project_code FROM projects WHERE id = ?', [$project_id]);
        $projectCode = $project['project_code'] ?? 'PRJ';
        $row = DB::selectOne('SELECT COUNT(*) as count FROM project_tasks WHERE project_id = ?', [$project_id]);
        $count = $row['count'] + 1;
        $shortCode = substr($projectCode, -4);

        return 'TASK-'.$shortCode.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('getAllProjects')) {
    function getAllProjects(): array
    {
        return DB::select('SELECT p.*,
              CONCAT(creator.full_name) as created_by_name,
              (SELECT GROUP_CONCAT(department) FROM project_departments WHERE project_id = p.id) as departments,
              (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id) as task_count
              FROM projects p
              LEFT JOIN employees creator ON p.created_by = creator.id
              GROUP BY p.id
              ORDER BY p.created_at DESC');
    }
}

if (! function_exists('getUserProjects')) {
    function getUserProjects($employee_id): array
    {
        return DB::select("SELECT DISTINCT p.*,
              CONCAT(creator.full_name) as created_by_name,
              (SELECT GROUP_CONCAT(department) FROM project_departments WHERE project_id = p.id) as departments,
              (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id) as task_count,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id AND status = 'Done') as completed_count
              FROM projects p
              LEFT JOIN project_members pm ON p.id = pm.project_id
              LEFT JOIN employees creator ON p.created_by = creator.id
              WHERE pm.employee_id = ? OR p.created_by = ?
              GROUP BY p.id
              ORDER BY p.priority DESC, p.created_at DESC", [$employee_id, $employee_id]);
    }
}

if (! function_exists('getActiveProjectsForUser')) {
    function getActiveProjectsForUser($employee_id): array
    {
        return DB::select("SELECT DISTINCT p.*,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id AND status != 'Done') as pending_tasks,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id) as total_tasks,
              (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id AND status = 'Done') as completed_count,
              (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count
              FROM projects p
              LEFT JOIN project_members pm ON p.id = pm.project_id
              WHERE (pm.employee_id = ? OR p.created_by = ?)
              AND p.status IN ('Planning', 'Active')
              GROUP BY p.id
              ORDER BY p.priority DESC, p.created_at DESC", [$employee_id, $employee_id]);
    }
}

if (! function_exists('canViewProject')) {
    function canViewProject($project_id, $employee_id): bool
    {
        return count(DB::select('SELECT p.id FROM projects p
              LEFT JOIN project_members pm ON p.id = pm.project_id
              WHERE p.id = ?
              AND (pm.employee_id = ? OR p.created_by = ?)
              LIMIT 1', [$project_id, $employee_id, $employee_id])) > 0;
    }
}

if (! function_exists('isProjectAdmin')) {
    function isProjectAdmin($project_id, $employee_id): bool
    {
        return count(DB::select("SELECT p.id FROM projects p
              LEFT JOIN project_members pm ON p.id = pm.project_id
              WHERE p.id = ?
              AND (p.created_by = ? OR (pm.employee_id = ? AND pm.role = 'Admin'))
              LIMIT 1", [$project_id, $employee_id, $employee_id])) > 0;
    }
}

if (! function_exists('getProjectStats')) {
    function getProjectStats($project_id): array
    {
        $total = $completed = $inProgress = $overdue = 0;
        foreach (DB::select('SELECT status, due_date FROM project_tasks WHERE project_id = ?', [$project_id]) as $task) {
            $total++;
            if ($task['status'] == 'Done') {
                $completed++;
            } elseif ($task['status'] == 'In Progress') {
                $inProgress++;
            }
            if ($task['status'] != 'Done' && ! empty($task['due_date']) && strtotime($task['due_date']) < time()) {
                $overdue++;
            }
        }

        return [
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'in_progress_tasks' => $inProgress,
            'overdue_tasks' => $overdue,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }
}

if (! function_exists('getUserProjectTasks')) {
    function getUserProjectTasks($employee_id): array
    {
        return DB::select("SELECT t.*, p.title as project_title, p.id as project_id,
              CONCAT(assigner.full_name) as assigned_by_name
              FROM project_tasks t
              LEFT JOIN projects p ON t.project_id = p.id
              LEFT JOIN employees assigner ON t.assigned_by = assigner.id
              WHERE t.assigned_to = ?
              AND p.status != 'Cancelled'
              ORDER BY FIELD(t.status, 'Blocked', 'To Do', 'In Progress', 'Review', 'Done'),
                       t.due_date ASC, t.priority DESC", [$employee_id]);
    }
}

if (! function_exists('updateTaskStatus')) {
    function updateTaskStatus($task_id, $new_status, $employee_id, $notes = ''): bool
    {
        $task = DB::selectOne('SELECT status FROM project_tasks WHERE id = ?', [$task_id]);
        $old_status = $task['status'] ?? null;
        if ($old_status === $new_status) {
            return true;
        }
        $completed_date = ($new_status == 'Done') ? date('Y-m-d H:i:s') : null;
        try {
            DB::update('UPDATE project_tasks SET status = ?, completed_date = ? WHERE id = ?', [$new_status, $completed_date, $task_id]);
        } catch (\Throwable $e) {
            return false;
        }
        DB::insert('INSERT INTO project_task_status_history (task_id, from_status, to_status, changed_by, notes) VALUES (?, ?, ?, ?, ?)',
            [$task_id, $old_status ?: null, $new_status, $employee_id, $notes]);

        return true;
    }
}

if (! function_exists('getStatusColor')) {
    function getStatusColor($status): string
    {
        $colors = [
            'To Do' => '#94a3b8',
            'In Progress' => '#2563eb',
            'Review' => '#7c3aed',
            'Done' => '#16a34a',
            'Blocked' => '#dc2626',
            'Planning' => '#f59e0b',
            'Active' => '#2563eb',
            'On Hold' => '#f59e0b',
            'Completed' => '#16a34a',
            'Cancelled' => '#dc2626',
        ];

        return $colors[$status] ?? '#94a3b8';
    }
}

if (! function_exists('isAdminUser')) {
    function isAdminUser($employee_id): bool
    {
        $user = DB::selectOne('SELECT role FROM employees WHERE id = ? AND status = 1', [$employee_id]);

        return $user && $user['role'] == 'admin';
    }
}
