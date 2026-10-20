<?php

namespace App\Http\Controllers;

use App\Models\ProjectDepartment;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Project management pages and the employee rewards news feed
 * (was project-detail, projects-admin, projects-dashboard, rewards, reward-detail).
 */
class ProjectsController extends Controller
{
    /** project-detail */
    public function detail(Request $request)
    {
        // Redirect if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $employee_id = session('employee_id');
        $project_id = $request->query('id') !== null ? (int) $request->query('id') : 0;

        if ($project_id <= 0) {
            kc_redirect('projects-dashboard');
        }

        // Check if user can view this project (must be member or creator)
        if (! canViewProject($project_id, $employee_id)) {
            kc_redirect('projects-dashboard');
        }

        // Get project details
        $project = DB::selectOne('SELECT p.*, CONCAT(e.full_name) as created_by_name
          FROM projects p
          LEFT JOIN employees e ON p.created_by = e.id
          WHERE p.id = ?', [$project_id]);

        if (! $project) {
            kc_redirect('projects-dashboard');
        }

        // Get project stats
        $stats = getProjectStats($project_id);

        // Get project members
        $members = DB::select('SELECT pm.*, e.full_name, e.email, e.photo, e.department
                 FROM project_members pm
                 LEFT JOIN employees e ON pm.employee_id = e.id
                 WHERE pm.project_id = ?
                 ORDER BY pm.role, e.full_name', [$project_id]);

        // Get project departments
        $departmentList = [];
        foreach (DB::select('SELECT department FROM project_departments WHERE project_id = ?', [$project_id]) as $dept) {
            $departmentList[] = $dept['department'];
        }

        // Get tasks + department-wise task stats
        [$taskData, $deptTaskStats] = $this->loadTasks($project_id, $departmentList);

        // Check if user is admin
        $isAdmin = isProjectAdmin($project_id, $employee_id);

        // Handle form submissions
        $message = '';
        $messageType = '';

        if ($request->isMethod('post') && $request->post('action') !== null) {
            $redirect_url = 'project-detail?id='.$project_id;

            // ADD TASK
            if ($request->post('action') === 'add_task') {
                $title = sanitize($request->input('title'));
                $description = sanitize($request->input('description'));
                $assigned_to = (int) $request->input('assigned_to');
                $priority = sanitize($request->input('priority'));
                $status = sanitize($request->input('status'));
                $due_date = ! empty($request->input('due_date')) ? sanitize($request->input('due_date')) : null;
                $department = sanitize($request->input('department') ?? '');

                $task_code = generateTaskCode($project_id);

                DB::insert('INSERT INTO project_tasks (project_id, department, task_code, title, description, assigned_to, assigned_by, priority, status, due_date)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$project_id, $department, $task_code, $title, $description, $assigned_to, $employee_id, $priority, $status, $due_date ?: null]);
                $task_id = DB::getPdo()->lastInsertId();

                DB::insert("INSERT INTO project_task_status_history (task_id, from_status, to_status, changed_by, notes)
                    VALUES (?, NULL, ?, ?, 'Task created')", [$task_id, $status, $employee_id]);

                // e-mail the assignee (app/Support/projects.php; never breaks the request)
                sendTaskAssignmentNotification($task_id);

                kc_redirect($redirect_url.'&msg=task_added');
            }

            // UPDATE TASK STATUS
            if ($request->post('action') === 'update_task_status') {
                $task_id = (int) $request->input('task_id');
                $new_status = sanitize($request->input('status'));
                $notes = sanitize($request->input('notes') ?? '');

                $taskCheck = DB::selectOne('SELECT assigned_to, title FROM project_tasks WHERE id = ?', [$task_id]);

                if ($taskCheck && ($taskCheck['assigned_to'] == $employee_id || $isAdmin)) {
                    if (updateTaskStatus($task_id, $new_status, $employee_id, $notes)) {
                        kc_redirect($redirect_url.'&msg=status_updated');
                    } else {
                        $message = 'Error updating task status.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = "You don't have permission to update this task. Only the assigned user or admin can update status.";
                    $messageType = 'danger';
                }
            }
        }

        // Handle GET messages
        if ($request->has('msg') && $request->query('msg') !== null) {
            $msg = $request->query('msg');
            if ($msg === 'task_added') {
                $message = 'Task created successfully!';
                $messageType = 'success';
                [$taskData, $deptTaskStats] = $this->loadTasks($project_id, $departmentList);
            } elseif ($msg === 'status_updated') {
                $message = 'Task status updated successfully!';
                $messageType = 'success';
                [$taskData, $deptTaskStats] = $this->loadTasks($project_id, $departmentList);
            } elseif ($msg === 'task_deleted') {
                $message = 'Task deleted successfully!';
                $messageType = 'success';
                [$taskData, $deptTaskStats] = $this->loadTasks($project_id, $departmentList);
            }
        }

        // Handle task deletion (admin only)
        $deleteTask = $request->query('delete_task');
        if ($deleteTask !== null && is_numeric($deleteTask) && $isAdmin) {
            $task_id = (int) $deleteTask;
            DB::delete('DELETE FROM project_tasks WHERE id = ?', [$task_id]);
            kc_redirect('project-detail?id='.$project_id.'&msg=task_deleted');
        }

        return view('project-detail', compact(
            'employee_id', 'project_id', 'project', 'stats', 'members', 'departmentList',
            'taskData', 'deptTaskStats', 'isAdmin', 'message', 'messageType'
        ));
    }

    /** Tasks of a project and the per-department counts (old inline code + refreshPageData()). */
    private function loadTasks($project_id, array $departmentList): array
    {
        $taskData = DB::select('SELECT t.*,
               CONCAT(e.full_name) as assigned_to_name,
               CONCAT(assigner.full_name) as assigned_by_name
               FROM project_tasks t
               LEFT JOIN employees e ON t.assigned_to = e.id
               LEFT JOIN employees assigner ON t.assigned_by = assigner.id
               WHERE t.project_id = ?
               ORDER BY t.priority DESC, t.created_at DESC', [intval($project_id)]);

        $deptTaskStats = [];
        foreach ($departmentList as $dept) {
            $total = $completed = $inProgress = $todo = $blocked = 0;
            foreach ($taskData as $task) {
                if ($task['department'] == $dept) {
                    $total++;
                    if ($task['status'] == 'Done') {
                        $completed++;
                    } elseif ($task['status'] == 'In Progress') {
                        $inProgress++;
                    } elseif ($task['status'] == 'To Do') {
                        $todo++;
                    } elseif ($task['status'] == 'Blocked') {
                        $blocked++;
                    }
                }
            }
            $deptTaskStats[$dept] = [
                'total' => $total,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'todo' => $todo,
                'blocked' => $blocked,
                'completion' => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        }

        return [$taskData, $deptTaskStats];
    }

    /** projects-admin */
    public function admin(Request $request)
    {
        // Redirect if not logged in
        if (! isLoggedIn()) {
            kc_redirect('../login');
        }

        $employee_id = session('employee_id');
        $employee_name = session('employee_name');
        $employee_role = session('employee_role') ?? 'employee';

        // Check if user is admin for full access
        $isAdmin = ($employee_role == 'admin');

        $message = '';
        $messageType = '';

        // CREATE PROJECT - Anyone can create
        if ($request->isMethod('post') && $request->post('action') !== null && $request->post('action') === 'create') {
            $title = sanitize($request->input('title'));
            $description = sanitize($request->input('description'));
            $start_date = sanitize($request->input('start_date'));
            $end_date = ! empty($request->input('end_date')) ? sanitize($request->input('end_date')) : null;
            $priority = sanitize($request->input('priority'));
            $status = sanitize($request->input('status'));
            $departments = $request->input('departments') ?? [];
            $selected_members = $request->input('members') ?? [];

            // Validate title uniqueness
            if (count(DB::select("SELECT id FROM projects WHERE title = ? AND status != 'Cancelled'", [$title])) > 0) {
                $message = 'A project with this title already exists. Please use a different title.';
                $messageType = 'danger';
            } else {
                $project_code = generateProjectCode();

                DB::insert('INSERT INTO projects (project_code, title, description, start_date, end_date, priority, status, created_by)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$project_code, $title, $description, $start_date, $end_date ?: null, $priority, $status, $employee_id]);
                $project_id = DB::getPdo()->lastInsertId();

                // Add departments
                foreach ($departments as $dept) {
                    $dept = sanitize($dept);
                    DB::insert('INSERT INTO project_departments (project_id, department) VALUES (?, ?)', [$project_id, $dept]);
                }

                // Add creator as admin member
                DB::insert("INSERT INTO project_members (project_id, employee_id, role) VALUES (?, ?, 'Admin')", [$project_id, $employee_id]);

                // Add selected members
                $addedMembers = [];
                foreach ($selected_members as $member_id) {
                    $member_id = (int) $member_id;
                    if ($member_id != $employee_id) {
                        DB::insert('INSERT INTO project_members (project_id, employee_id, role) VALUES (?, ?, ?)', [$project_id, $member_id, 'Member']);

                        $memRow = DB::selectOne('SELECT full_name, email FROM employees WHERE id = ? AND status = 1', [$member_id]);
                        if ($memRow) {
                            $addedMembers[] = ['id' => $member_id, 'name' => $memRow['full_name'], 'email' => $memRow['email']];
                        }
                    }
                }

                // Send email notifications
                sendProjectMemberInvitation($addedMembers, $title, $project_code, $project_id, $employee_name);

                kc_redirect('../projects-dashboard?msg=project_created');
            }
        }

        // UPDATE PROJECT - Only creator or admin
        if ($request->isMethod('post') && $request->post('action') !== null && $request->post('action') === 'update') {
            $project_id = (int) $request->input('project_id');
            $title = sanitize($request->input('title'));
            $description = sanitize($request->input('description'));
            $start_date = sanitize($request->input('start_date'));
            $end_date = ! empty($request->input('end_date')) ? sanitize($request->input('end_date')) : null;
            $priority = sanitize($request->input('priority'));
            $status = sanitize($request->input('status'));
            $departments = $request->input('departments') ?? [];
            $selected_members = $request->input('members') ?? [];

            // Check if user is the creator or admin
            $creator = DB::selectOne('SELECT created_by FROM projects WHERE id = ?', [$project_id]);

            if (! $creator || ($creator['created_by'] != $employee_id && ! $isAdmin)) {
                $message = "You don't have permission to edit this project.";
                $messageType = 'danger';
            } elseif (count(DB::select("SELECT id FROM projects WHERE title = ? AND id != ? AND status != 'Cancelled'", [$title, $project_id])) > 0) {
                $message = 'Another project with this title already exists. Please use a different title.';
                $messageType = 'danger';
            } else {
                DB::update('UPDATE projects SET
                      title = ?,
                      description = ?,
                      start_date = ?,
                      end_date = ?,
                      priority = ?,
                      status = ?
                      WHERE id = ?', [$title, $description, $start_date, $end_date ?: null, $priority, $status, $project_id]);

                // Update departments
                DB::delete('DELETE FROM project_departments WHERE project_id = ?', [$project_id]);
                foreach ($departments as $dept) {
                    $dept = sanitize($dept);
                    DB::insert('INSERT INTO project_departments (project_id, department) VALUES (?, ?)', [$project_id, $dept]);
                }

                // Get existing members
                $existingMembers = [];
                foreach (DB::select('SELECT employee_id FROM project_members WHERE project_id = ?', [$project_id]) as $row) {
                    $existingMembers[] = $row['employee_id'];
                }

                // Add new members
                $addedMembers = [];
                foreach ($selected_members as $member_id) {
                    $member_id = (int) $member_id;
                    if (! in_array($member_id, $existingMembers)) {
                        DB::insert('INSERT INTO project_members (project_id, employee_id, role) VALUES (?, ?, ?)', [$project_id, $member_id, 'Member']);

                        $memRow = DB::selectOne('SELECT full_name, email FROM employees WHERE id = ? AND status = 1', [$member_id]);
                        if ($memRow) {
                            $addedMembers[] = ['id' => $member_id, 'name' => $memRow['full_name'], 'email' => $memRow['email']];
                        }
                    }
                }

                $project_code = DB::selectOne('SELECT project_code FROM projects WHERE id = ?', [$project_id])['project_code'] ?? '';

                if (! empty($addedMembers)) {
                    sendProjectMemberInvitation($addedMembers, $title, $project_code, $project_id, $employee_name);
                }

                $message = 'Project updated successfully! '.count($addedMembers).' new members added and notified.';
                $messageType = 'success';
            }
        }

        // DELETE PROJECT - ONLY CREATOR OR ADMIN
        $delete = $request->query('delete');
        if ($delete !== null && is_numeric($delete)) {
            $project_id = (int) $delete;

            $creator = DB::selectOne('SELECT created_by, title FROM projects WHERE id = ?', [$project_id]);

            if (! $creator) {
                $message = 'Project not found.';
                $messageType = 'danger';
            } elseif ($creator['created_by'] == $employee_id || $isAdmin) {
                $projectTitle = $creator['title'];
                DB::delete('DELETE FROM projects WHERE id = ?', [$project_id]);
                $message = "Project '".htmlspecialchars($projectTitle)."' deleted successfully!";
                $messageType = 'success';
            } else {
                $message = "❌ You don't have permission to delete this project. Only the project creator or admin can delete it.";
                $messageType = 'danger';
            }
        }

        // GET DATA - Admins see all, others see only their projects
        if ($isAdmin) {
            $projects = getAllProjects();
        } else {
            $projects = DB::select('SELECT DISTINCT p.*,
                      CONCAT(creator.full_name) as created_by_name,
                      (SELECT GROUP_CONCAT(department) FROM project_departments WHERE project_id = p.id) as departments,
                      (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
                      (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id) as task_count
                      FROM projects p
                      LEFT JOIN project_members pm ON p.id = pm.project_id
                      LEFT JOIN employees creator ON p.created_by = creator.id
                      WHERE pm.employee_id = ? OR p.created_by = ?
                      GROUP BY p.id
                      ORDER BY p.created_at DESC', [$employee_id, $employee_id]);
        }

        // Creator name per project (the old page queried this inside the table loop)
        $creatorNames = [];
        foreach ($projects as $project) {
            $creatorRow = DB::selectOne('SELECT full_name FROM employees WHERE id = ?', [$project['created_by']]);
            $creatorNames[$project['id']] = $creatorRow ? $creatorRow['full_name'] : 'Unknown';
        }

        // DEPARTMENT LIST: the standard list, plus any other department names used by employees or projects
        $departmentList = [
            'Administration',
            'Finance & Accounts',
            'Human Resources',
            'Information Technology',
            'Logistics',
            'Procurement & MIS',
            'Sales & Marketing',
            'Warehouse',
            'Internal Audit',
            'Legal & Compliance',
            'Production',
            'Quality',
        ];
        $extraDepartments = DB::select("SELECT DISTINCT TRIM(department) as name FROM employees WHERE status = 1 AND department IS NOT NULL AND TRIM(department) != ''
            UNION SELECT DISTINCT TRIM(department) FROM project_departments WHERE TRIM(department) != '' ORDER BY name");
        foreach ($extraDepartments as $d) {
            if (! in_array($d['name'], $departmentList, true)) {
                $departmentList[] = $d['name'];
            }
        }

        // Get all active employees for member selection
        $allEmployees = DB::select('SELECT id, employee_code, full_name, email, department, photo FROM employees WHERE status = 1 ORDER BY full_name ASC');

        // Get project for edit
        $editProject = null;
        $editProjectMembers = [];
        $edit = $request->query('edit');
        if ($edit !== null && is_numeric($edit)) {
            $editId = (int) $edit;

            $permRow = DB::selectOne('SELECT created_by FROM projects WHERE id = ?', [$editId]);

            if ($permRow && ($permRow['created_by'] == $employee_id || $isAdmin)) {
                $editProject = DB::selectOne('SELECT p.*,
                  (SELECT GROUP_CONCAT(department) FROM project_departments WHERE project_id = p.id) as departments_list
                  FROM projects p WHERE p.id = ?', [$editId]);

                if ($editProject) {
                    foreach (DB::select('SELECT employee_id FROM project_members WHERE project_id = ?', [$editId]) as $row) {
                        $editProjectMembers[] = $row['employee_id'];
                    }
                }
            }
        }

        return view('projects-admin', compact(
            'employee_id', 'employee_name', 'isAdmin', 'message', 'messageType', 'projects',
            'creatorNames', 'departmentList', 'allEmployees', 'editProject', 'editProjectMembers'
        ));
    }

    /** projects-dashboard */
    public function dashboard(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $employee_id = session('employee_id');
        $employee_name = session('employee_name');

        // Get all projects where user is a member OR creator (NOT by department only)
        $allProjects = DB::select("SELECT DISTINCT p.*,
                     CONCAT(creator.full_name) as created_by_name,
                     (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id) as task_count,
                     (SELECT COUNT(*) FROM project_tasks WHERE project_id = p.id AND status = 'Done') as completed_count,
                     (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count
                     FROM projects p
                     LEFT JOIN project_members pm ON p.id = pm.project_id
                     LEFT JOIN employees creator ON p.created_by = creator.id
                     WHERE (pm.employee_id = ? OR p.created_by = ?)
                     AND p.status IN ('Planning', 'Active')
                     GROUP BY p.id
                     ORDER BY p.priority DESC, p.created_at DESC", [$employee_id, $employee_id]);

        // Get tasks assigned to the user
        $myTasks = getUserProjectTasks($employee_id);

        // Get stats
        $totalProjects = count($allProjects);
        $totalTasks = count($myTasks);
        $completedTasks = 0;
        $pendingTasks = 0;
        $overdueTasks = 0;

        foreach ($myTasks as $task) {
            if ($task['status'] == 'Done') {
                $completedTasks++;
            } else {
                $pendingTasks++;
                if (! empty($task['due_date']) && strtotime($task['due_date']) < time()) {
                    $overdueTasks++;
                }
            }
        }

        // Check if user can create projects (everyone can)
        $canCreateProjects = true;

        return view('projects-dashboard', compact(
            'employee_id', 'employee_name', 'allProjects', 'myTasks', 'totalProjects', 'totalTasks',
            'completedTasks', 'pendingTasks', 'overdueTasks', 'canCreateProjects'
        ));
    }
    /** rewards */
    public function rewards(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // Get all active rewards items
        $rewards = DB::select('SELECT ri.*, 
          (SELECT COUNT(*) FROM rewards_comments WHERE rewards_item_id = ri.id AND status = 1) as comment_count,
          (SELECT COUNT(*) FROM rewards_attachments WHERE rewards_item_id = ri.id) as attachment_count
          FROM rewards_items ri 
          WHERE ri.status = 1 
          ORDER BY ri.sort_order, ri.created_at DESC');

        return view('rewards', compact('rewards'));
    }

    /** reward-detail */
    public function rewardDetail(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // Get reward ID from URL
        $reward_id = $request->query('id') !== null ? (int) $request->query('id') : 0;

        if (! $reward_id) {
            kc_redirect('rewards');
        }

        // Get the reward details
        $reward = DB::selectOne('SELECT * FROM rewards_items WHERE id = ? AND status = 1', [$reward_id]);

        if (! $reward) {
            kc_redirect('rewards');
        }

        // Track view (only track once per session per reward)
        if (! session()->has('viewed_reward_'.$reward_id)) {
            DB::update('UPDATE rewards_items SET views_count = views_count + 1 WHERE id = ?', [$reward_id]);
            session(['viewed_reward_'.$reward_id => true]);
        }

        // Get attachments
        $attachments = DB::select('SELECT * FROM rewards_attachments WHERE rewards_item_id = ? ORDER BY created_at DESC', [$reward_id]);

        // Get comments with employee details
        $comments = DB::select('SELECT rc.*, e.photo as employee_photo 
                                 FROM rewards_comments rc 
                                 LEFT JOIN employees e ON rc.employee_id = e.id 
                                 WHERE rc.rewards_item_id = ? AND rc.status = 1 
                                 ORDER BY rc.created_at DESC', [$reward_id]);

        // Handle comment submission
        if ($request->isMethod('post') && $request->post('add_comment') !== null) {
            $comment = trim((string) $request->post('comment'));
            $employee_id = (int) session('employee_id');
            $employee_name = session('employee_name');
            $employee_photo = session()->has('employee_photo') && session('employee_photo') !== null ? session('employee_photo') : null;

            if (! empty($comment)) {
                DB::insert('INSERT INTO rewards_comments (rewards_item_id, employee_id, employee_name, employee_photo, comment) 
                VALUES (?, ?, ?, ?, ?)', [$reward_id, $employee_id, $employee_name, $employee_photo ?: null, $comment]);

                // Success - redirect to refresh page and show new comment
                kc_redirect('reward-detail?id='.$reward_id);
            }
        }

        // Get comment counts
        $comment_count = DB::selectOne('SELECT COUNT(*) as count FROM rewards_comments WHERE rewards_item_id = ? AND status = 1', [$reward_id])['count'];

        // Generate video embed URL
        $video_embed_url = '';
        if ($reward['video_type'] == 'youtube' && ! empty($reward['video_id'])) {
            $video_embed_url = "https://www.youtube.com/embed/{$reward['video_id']}";
        } elseif ($reward['video_type'] == 'vimeo' && ! empty($reward['video_id'])) {
            $video_embed_url = "https://player.vimeo.com/video/{$reward['video_id']}";
        } else {
            $video_embed_url = $reward['video_url'];
        }

        return view('reward-detail', compact('reward_id', 'reward', 'attachments', 'comments', 'comment_count', 'video_embed_url'));
    }
}
