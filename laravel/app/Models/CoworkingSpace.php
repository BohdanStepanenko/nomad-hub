<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoworkingSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'country_id',
        'hours',
        'cost',
        'wifi_speed',
        'has_coffee',
        'is_24_7',
        'website',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CoworkingReview::class);
    }
}
