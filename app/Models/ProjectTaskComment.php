<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `project_task_comments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $task_id  int(11)
 * @property string $employee_id  int(11)
 * @property string $comment  text
 * @property string $created_at  timestamp
 */
class ProjectTaskComment extends Model
{
    protected $table = 'project_task_comments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id', 'id');
    }
}
