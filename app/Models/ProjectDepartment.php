<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `project_departments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $project_id  int(11)
 * @property string $department  varchar(100)
 * @property string $created_at  timestamp
 */
class ProjectDepartment extends Model
{
    protected $table = 'project_departments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
