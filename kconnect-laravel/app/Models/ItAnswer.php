<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `it_answers` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $ticket_id  int(11)
 * @property string $question_id  int(11)
 * @property string $answer  text
 * @property string $created_at  timestamp
 */
class ItAnswer extends Model
{
    protected $table = 'it_answers';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ItQuestion::class, 'question_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ItTicket::class, 'ticket_id', 'id');
    }
}
