<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_post_id',
        'user_id',
        'content',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forumPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class);
    }
}
