<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'name',
        'address',
        'type',
        'total_units',
        'amenities',
        'documents',
    ];

    protected $casts = [
        'amenities' => 'array',
        'documents' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function occupiedUnits(): HasMany
    {
        return $this->units()->where('status', 'occupied');
    }

    public function vacantUnits(): HasMany
    {
        return $this->units()->where('status', 'vacant');
    }
}
