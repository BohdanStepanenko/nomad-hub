<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoworkingReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'coworking_space_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function coworkingSpace(): BelongsTo
    {
        return $this->belongsTo(CoworkingSpace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
