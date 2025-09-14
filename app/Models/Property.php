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

    // Add these methods relateed to lease to your existing Property model for enhanced lease tracking
    /**
     * Get all leases for units in this property
     */
    public function leases()
    {
        return Lease::whereHas('unit', function ($query) {
            $query->where('property_id', $this->id);
        });
    }

    /**
     * Get active leases count for this property
     */
    public function getActiveLeasesCountAttribute(): int
    {
        return $this->leases()->where('status', 'active')->count();
    }

    /**
     * Get occupied units count
     */
    public function getOccupiedUnitsCountAttribute(): int
    {
        return $this->units()->where('status', 'occupied')->count();
    }

    /**
     * Get vacancy rate as percentage
     */
    public function getVacancyRateAttribute(): float
    {
        if ($this->total_units == 0) return 0;
        $vacant = $this->total_units - $this->occupied_units_count;
        return round(($vacant / $this->total_units) * 100, 1);
    }
}
