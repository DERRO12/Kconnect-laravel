<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `it_ticket_attachments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $ticket_id  int(11)
 * @property string|null $comment_id  int(11)
 * @property string $file_name  varchar(255)
 * @property string $file_path  varchar(500)
 * @property string|null $file_size  int(11)
 * @property string|null $file_type  varchar(100)
 * @property string $uploaded_by  int(11)
 * @property string $created_at  timestamp
 */
class ItTicketAttachment extends Model
{
    protected $table = 'it_ticket_attachments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(ItTicketComment::class, 'comment_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ItTicket::class, 'ticket_id', 'id');
    }
}
