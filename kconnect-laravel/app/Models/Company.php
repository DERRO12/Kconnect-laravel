<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Table `companies` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $name  varchar(100)
 * @property string|null $code  varchar(20)
 * @property string|null $description  text
 * @property string|null $logo  varchar(255)
 * @property string|null $address  text
 * @property string|null $phone  varchar(50)
 * @property string|null $email  varchar(100)
 * @property string|null $website  varchar(255)
 * @property string|null $sort_order  int(11)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class Company extends Model
{
    protected $table = 'companies';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'company_id', 'id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
}
