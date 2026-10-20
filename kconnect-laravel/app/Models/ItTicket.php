<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `it_tickets` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $ticket_number  varchar(50)
 * @property string $employee_id  int(11)
 * @property string $category  enum('Hardware','Software','Network','ERP','Printer','SFA','Email','Other')
 * @property string $priority  enum('Low','Medium','High','Urgent')
 * @property string $subject  varchar(255)
 * @property string $description  text
 * @property string|null $assigned_to  int(11)
 * @property string $status  enum('New','In Progress','Resolved','Closed','Rejected')
 * @property string|null $admin_notes  text
 * @property string|null $resolution_notes  text
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 * @property string|null $resolved_at  datetime
 * @property string|null $closed_at  datetime
 * @property string|null $created_by_admin  tinyint(1)
 */
class ItTicket extends Model
{
    protected $table = 'it_tickets';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function itAnswers(): HasMany
    {
        return $this->hasMany(ItAnswer::class, 'ticket_id', 'id');
    }

    public function itTicketAssignments(): HasMany
    {
        return $this->hasMany(ItTicketAssignment::class, 'ticket_id', 'id');
    }

    public function itTicketAttachments(): HasMany
    {
        return $this->hasMany(ItTicketAttachment::class, 'ticket_id', 'id');
    }

    public function itTicketComments(): HasMany
    {
        return $this->hasMany(ItTicketComment::class, 'ticket_id', 'id');
    }

    public function itTicketStatusHistories(): HasMany
    {
        return $this->hasMany(ItTicketStatusHistory::class, 'ticket_id', 'id');
    }
}
