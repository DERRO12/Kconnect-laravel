<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `project_tasks` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $project_id  int(11)
 * @property string|null $department  varchar(100)
 * @property string $task_code  varchar(50)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string|null $assigned_to  int(11)
 * @property string $assigned_by  int(11)
 * @property string|null $priority  enum('Low','Medium','High','Urgent')
 * @property string|null $status  enum('To Do','In Progress','Review','Done','Blocked')
 * @property string|null $due_date  date
 * @property string|null $start_date  date
 * @property string|null $completed_date  datetime
 * @property string|null $completion_notes  text
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class ProjectTask extends Model
{
    protected $table = 'project_tasks';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by', 'id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function projectTaskAttachments(): HasMany
    {
        return $this->hasMany(ProjectTaskAttachment::class, 'task_id', 'id');
    }

    public function projectTaskComments(): HasMany
    {
        return $this->hasMany(ProjectTaskComment::class, 'task_id', 'id');
    }

    public function projectTaskStatusHistories(): HasMany
    {
        return $this->hasMany(ProjectTaskStatusHistory::class, 'task_id', 'id');
    }
}
