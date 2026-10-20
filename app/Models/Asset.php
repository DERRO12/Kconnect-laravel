<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `assets` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $asset_code  varchar(50)
 * @property string $asset_name  varchar(255)
 * @property string $category  enum('Laptop','Desktop','Monitor','Printer','Phone','Tablet','Networking','Server','Other IT','Furniture','Vehicle','Other')
 * @property string|null $brand  varchar(100)
 * @property string|null $model  varchar(100)
 * @property string|null $serial_number  varchar(100)
 * @property string|null $specifications  text
 * @property string|null $ram  varchar(50)
 * @property string|null $storage  varchar(50)
 * @property string|null $processor  varchar(100)
 * @property string|null $purchase_date  date
 * @property string|null $purchase_cost  decimal(12,2)
 * @property string|null $warranty_expiry  date
 * @property string $condition_status  enum('New','Excellent','Good','Fair','Poor','Damaged','Retired')
 * @property string|null $notes  text
 * @property string|null $image  varchar(255)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class Asset extends Model
{
    protected $table = 'assets';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id', 'id');
    }

    public function assetMaintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id', 'id');
    }
}
