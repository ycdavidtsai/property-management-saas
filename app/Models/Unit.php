<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'property_id',
        'unit_number',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'rent_amount',
        'status',
    ];

    protected $casts = [
        'bathrooms' => 'decimal:1',
        'rent_amount' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function isVacant(): bool
    {
        return $this->status === 'vacant';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
