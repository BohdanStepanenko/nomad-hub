<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Housing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'country_id',
        'city',
        'address',
        'price',
        'created_at',
        'updated_at',
    ];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'country_id' => $this->country_id,
            'city' => $this->city,
            'address' => $this->address,
            'price' => $this->price,
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function favoriteHousings(): HasMany
    {
        return $this->hasMany(FavoriteHousing::class);
    }
}
