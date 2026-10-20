<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `employees` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $employee_code  varchar(50)
 * @property string $full_name  varchar(255)
 * @property string|null $email  varchar(255)
 * @property string|null $phone  varchar(50)
 * @property string|null $extension  varchar(20)
 * @property string|null $mobile  varchar(50)
 * @property string $password  varchar(255)
 * @property string|null $department  varchar(100)
 * @property string|null $company_id  int(11)
 * @property string|null $position  varchar(100)
 * @property string|null $birth_date  date
 * @property string|null $join_date  date
 * @property string|null $emergency_contact  varchar(255)
 * @property string|null $emergency_phone  varchar(50)
 * @property string|null $address  text
 * @property string|null $notes  text
 * @property string|null $photo  varchar(255)
 * @property string|null $role  enum('employee','admin','manager','hr','director')
 * @property string|null $status  tinyint(1)
 * @property string|null $is_verified  tinyint(1)
 * @property string|null $verification_token  varchar(255)
 * @property string|null $token_expires_at  datetime
 * @property string|null $registered_at  datetime
 * @property string|null $last_login  datetime
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class Employee extends Model
{
    protected $table = 'employees';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['password'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'assigned_by', 'id');
    }

    public function assetAssignmentsByEmployee(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'employee_id', 'id');
    }

    public function assetAssignmentsByReceivedBy(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'received_by', 'id');
    }

    public function itTickets(): HasMany
    {
        return $this->hasMany(ItTicket::class, 'assigned_to', 'id');
    }

    public function itTicketsByEmployee(): HasMany
    {
        return $this->hasMany(ItTicket::class, 'employee_id', 'id');
    }

    public function itTicketAssignments(): HasMany
    {
        return $this->hasMany(ItTicketAssignment::class, 'assigned_by', 'id');
    }

    public function itTicketAssignmentsByAssignedTo(): HasMany
    {
        return $this->hasMany(ItTicketAssignment::class, 'assigned_to', 'id');
    }

    public function itTicketComments(): HasMany
    {
        return $this->hasMany(ItTicketComment::class, 'employee_id', 'id');
    }

    public function itTicketStatusHistories(): HasMany
    {
        return $this->hasMany(ItTicketStatusHistory::class, 'changed_by', 'id');
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class, 'nominator_id', 'id');
    }

    public function nominationsByNominee(): HasMany
    {
        return $this->hasMany(Nomination::class, 'nominee_id', 'id');
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'employee_id', 'id');
    }

    public function projectTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'assigned_by', 'id');
    }

    public function projectTasksByAssignedTo(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'assigned_to', 'id');
    }

    public function projectTaskAttachments(): HasMany
    {
        return $this->hasMany(ProjectTaskAttachment::class, 'uploaded_by', 'id');
    }

    public function projectTaskComments(): HasMany
    {
        return $this->hasMany(ProjectTaskComment::class, 'employee_id', 'id');
    }

    public function projectTaskStatusHistories(): HasMany
    {
        return $this->hasMany(ProjectTaskStatusHistory::class, 'changed_by', 'id');
    }
}
