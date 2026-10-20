<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `it_settings` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $setting_key  varchar(100)
 * @property string|null $setting_value  text
 * @property string $updated_at  timestamp
 */
class ItSetting extends Model
{
    protected $table = 'it_settings';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
