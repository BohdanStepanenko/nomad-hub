<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visa extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'visa_type',
        'duration',
        'requirements',
        'cost',
        'source',
        'created_at',
        'updated_at',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
