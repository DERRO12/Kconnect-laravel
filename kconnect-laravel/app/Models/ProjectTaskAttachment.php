<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `project_task_attachments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $task_id  int(11)
 * @property string $file_name  varchar(255)
 * @property string $file_path  varchar(500)
 * @property string|null $file_size  int(11)
 * @property string|null $file_type  varchar(100)
 * @property string $uploaded_by  int(11)
 * @property string $created_at  timestamp
 */
class ProjectTaskAttachment extends Model
{
    protected $table = 'project_task_attachments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id', 'id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by', 'id');
    }
}
