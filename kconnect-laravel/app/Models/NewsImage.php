<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `news_images` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $news_id  int(11)
 * @property string $image_path  varchar(255)
 * @property string|null $sort_order  int(11)
 * @property string|null $caption  varchar(255)
 * @property string $created_at  timestamp
 */
class NewsImage extends Model
{
    protected $table = 'news_images';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function news(): BelongsTo
    {
        return $this->belongsTo(NewsPost::class, 'news_id', 'id');
    }
}
