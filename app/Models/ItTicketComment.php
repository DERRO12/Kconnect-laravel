<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `it_ticket_comments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $ticket_id  int(11)
 * @property string $employee_id  int(11)
 * @property string $comment  text
 * @property string|null $is_admin  tinyint(1)
 * @property string|null $has_images  tinyint(1)
 * @property string $created_at  timestamp
 */
class ItTicketComment extends Model
{
    protected $table = 'it_ticket_comments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ItTicket::class, 'ticket_id', 'id');
    }

    public function itTicketAttachments(): HasMany
    {
        return $this->hasMany(ItTicketAttachment::class, 'comment_id', 'id');
    }
}
