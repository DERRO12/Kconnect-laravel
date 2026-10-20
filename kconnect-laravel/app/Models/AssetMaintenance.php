<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `asset_maintenance` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $asset_id  int(11)
 * @property string $maintenance_date  date
 * @property string $maintenance_type  enum('Repair','Upgrade','Cleaning','Inspection','Software Update','Other')
 * @property string $description  text
 * @property string|null $cost  decimal(10,2)
 * @property string|null $performed_by  varchar(100)
 * @property string|null $next_maintenance_date  date
 * @property string|null $status  enum('Scheduled','In Progress','Completed','Cancelled')
 * @property string|null $notes  text
 * @property string $created_at  timestamp
 */
class AssetMaintenance extends Model
{
    protected $table = 'asset_maintenance';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
}
