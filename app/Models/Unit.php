<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * All leases for this unit
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * Get the currently active lease for this unit
     */
    public function activeLease()
    {
        return $this->leases()->where('status', 'active')->first();
    }

    /**
     * Get current tenants through the active lease
     */
    public function currentTenants()
    {
        $activeLease = $this->activeLease();
        return $activeLease ? $activeLease->tenants : collect();
    }

    /**
     * Check if unit is currently occupied (has active lease)
     */
    public function isOccupied(): bool
    {
        return $this->activeLease() !== null;
    }

    /**
     * Get tenant names as a string for display
     */
    public function getTenantNamesAttribute(): string
    {
        return $this->currentTenants()->pluck('name')->join(', ');
    }
}
