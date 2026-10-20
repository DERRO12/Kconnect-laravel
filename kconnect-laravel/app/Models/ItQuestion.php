<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `it_questions` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $question_text  text
 * @property string $question_type  enum('text','textarea','radio','checkbox','number','file')
 * @property string|null $options  text
 * @property string|null $is_required  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class ItQuestion extends Model
{
    protected $table = 'it_questions';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function itAnswers(): HasMany
    {
        return $this->hasMany(ItAnswer::class, 'question_id', 'id');
    }
}
