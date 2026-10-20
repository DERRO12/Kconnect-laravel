<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `nomination_settings` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $current_quarter  tinyint(1)
 * @property string $current_year  int(11)
 * @property string|null $quarter_start_date  date
 * @property string|null $quarter_end_date  date
 * @property string|null $cooldown_days  int(11)
 * @property string|null $max_nominations_per_quarter  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 * @property string|null $unique_votes_per_quarter  tinyint(1)
 */
class NominationSetting extends Model
{
    protected $table = 'nomination_settings';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
