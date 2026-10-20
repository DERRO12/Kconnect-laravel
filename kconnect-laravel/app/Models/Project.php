<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `projects` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $project_code  varchar(50)
 * @property string $title  varchar(255)
 * @property string|null $description  text
 * @property string $start_date  date
 * @property string|null $end_date  date
 * @property string|null $status  enum('Planning','Active','On Hold','Completed','Cancelled')
 * @property string|null $priority  enum('Low','Medium','High','Urgent')
 * @property string $created_by  int(11)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class Project extends Model
{
    protected $table = 'projects';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function projectDepartments(): HasMany
    {
        return $this->hasMany(ProjectDepartment::class, 'project_id', 'id');
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'id');
    }

    public function projectTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'project_id', 'id');
    }
}
