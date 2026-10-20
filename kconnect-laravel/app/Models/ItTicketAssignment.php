<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `it_ticket_assignments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $ticket_id  int(11)
 * @property string $assigned_to  int(11)
 * @property string $assigned_by  int(11)
 * @property string $created_at  timestamp
 */
class ItTicketAssignment extends Model
{
    protected $table = 'it_ticket_assignments';

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

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ItTicket::class, 'ticket_id', 'id');
    }
}
