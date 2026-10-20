<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `nomination_answers` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $nomination_id  int(11)
 * @property string $question_id  int(11)
 * @property string $answer  text
 * @property string $created_at  timestamp
 */
class NominationAnswer extends Model
{
    protected $table = 'nomination_answers';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
