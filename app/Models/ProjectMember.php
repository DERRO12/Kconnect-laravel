<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `project_members` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $project_id  int(11)
 * @property string $employee_id  int(11)
 * @property string|null $role  enum('Admin','Manager','Member','Viewer')
 * @property string $joined_at  timestamp
 */
class ProjectMember extends Model
{
    protected $table = 'project_members';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
