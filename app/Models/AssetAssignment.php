<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `asset_assignments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $asset_id  int(11)
 * @property string $employee_id  int(11)
 * @property string $assigned_date  datetime
 * @property string|null $expected_return_date  date
 * @property string|null $actual_return_date  datetime
 * @property string|null $condition_on_assignment  enum('New','Excellent','Good','Fair','Poor','Damaged')
 * @property string|null $condition_on_return  enum('New','Excellent','Good','Fair','Poor','Damaged')
 * @property string|null $assignment_notes  text
 * @property string|null $return_notes  text
 * @property string|null $assigned_by  int(11)
 * @property string|null $received_by  int(11)
 * @property string $status  enum('Active','Returned','Lost','Damaged','Transferred')
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class AssetAssignment extends Model
{
    protected $table = 'asset_assignments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by', 'id');
    }
}
