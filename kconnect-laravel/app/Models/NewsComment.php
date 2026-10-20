<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Table `news_comments` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $news_id  int(11)
 * @property string|null $commenter_name  varchar(100)
 * @property string $comment_text  text
 * @property string|null $status  tinyint(1)
 * @property string $created_at  timestamp
 */
class NewsComment extends Model
{
    protected $table = 'news_comments';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];

    public function news(): BelongsTo
    {
        return $this->belongsTo(NewsPost::class, 'news_id', 'id');
    }
}
