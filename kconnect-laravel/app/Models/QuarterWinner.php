<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `quarter_winners` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $category_id  int(11)
 * @property string $nomination_id  int(11)
 * @property string $nominee_id  int(11)
 * @property string $quarter  tinyint(1)
 * @property string $year  int(11)
 * @property string $winner_rank  tinyint(1)
 * @property string|null $announced_by  int(11)
 * @property string $announced_at  datetime
 * @property string $created_at  timestamp
 */
class QuarterWinner extends Model
{
    protected $table = 'quarter_winners';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
