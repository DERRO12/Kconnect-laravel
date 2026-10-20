<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `nominations` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $category_id  int(11)
 * @property string $nominator_id  int(11)
 * @property string $nominee_id  int(11)
 * @property string|null $status  enum('pending','reviewed','approved','rejected')
 * @property string|null $is_read  tinyint(1)
 * @property string|null $read_at  datetime
 * @property string|null $read_by  int(11)
 * @property string|null $admin_notes  text
 * @property string|null $level1_reviewed_by  int(11)
 * @property string|null $level1_reviewed_at  datetime
 * @property string|null $level1_notes  text
 * @property string|null $level1_decision  enum('pending','shortlisted','rejected')
 * @property string|null $level2_reviewed_by  int(11)
 * @property string|null $level2_reviewed_at  datetime
 * @property string|null $level2_notes  text
 * @property string|null $level2_decision  enum('pending','shortlisted','rejected')
 * @property string|null $level3_reviewed_by  int(11)
 * @property string|null $level3_reviewed_at  datetime
 * @property string|null $level3_notes  text
 * @property string|null $level3_decision  enum('pending','approved','rejected')
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 * @property string|null $quarter  tinyint(1)
 * @property string|null $year  int(11)
 * @property string|null $is_winner  tinyint(1)
 * @property string|null $winner_announced_at  datetime
 */
class Nomination extends Model
{
    protected $table = 'nominations';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(NominationCategory::class, 'category_id', 'id');
    }

    public function nominator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nominator_id', 'id');
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nominee_id', 'id');
    }
}
