<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `nomination_questions` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $category_id  int(11)
 * @property string $question_text  text
 * @property string|null $question_type  enum('text','long_text','number','radio','checkbox','rating','date','attachment')
 * @property string|null $options  text
 * @property string|null $is_required  tinyint(1)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class NominationQuestion extends Model
{
    protected $table = 'nomination_questions';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
