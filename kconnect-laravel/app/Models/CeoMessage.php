<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `ceo_messages` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $message  text
 * @property string|null $author  varchar(100)
 * @property string|null $read_more_url  varchar(255)
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class CeoMessage extends Model
{
    protected $table = 'ceo_messages';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
