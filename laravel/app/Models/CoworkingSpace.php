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

    protected $casts = [
        'has_coffee' => 'boolean',
        'is_24_7' => 'boolean',
    ];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country_id' => $this->country_id,
            'cost' => $this->cost,
            'wifi_speed' => $this->wifi_speed,
            'has_coffee' => $this->has_coffee,
            'is_24_7' => $this->is_24_7,
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CoworkingReview::class);
    }
}
