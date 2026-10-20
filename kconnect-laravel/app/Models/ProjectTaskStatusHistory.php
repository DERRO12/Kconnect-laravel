<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `project_task_status_history` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $task_id  int(11)
 * @property string|null $from_status  varchar(50)
 * @property string $to_status  varchar(50)
 * @property string $changed_by  int(11)
 * @property string|null $notes  text
 * @property string $created_at  timestamp
 */
class ProjectTaskStatusHistory extends Model
{
    protected $table = 'project_task_status_history';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by', 'id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id', 'id');
    }
}
