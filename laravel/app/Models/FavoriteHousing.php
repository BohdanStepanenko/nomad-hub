<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteHousing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'housing_id',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function housing(): BelongsTo
    {
        return $this->belongsTo(Housing::class);
    }
}
