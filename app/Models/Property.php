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
        return $this->hasManyThrough(
            \App\Models\Lease::class,
            \App\Models\Unit::class,
            'property_id', // Foreign key on units table
            'unit_id',     // Foreign key on leases table
            'id',          // Local key on properties table
            'id'           // Local key on units table
        );
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

    // Add these methods to your existing app/Models/Property.php file

    /**
     * Get all current tenants in this property
     */
    public function currentTenants()
    {
        // Get all active leases for units in this property
        $activeLeases = \App\Models\Lease::where('status', 'active')
            ->whereHas('unit', function ($query) {
                $query->where('property_id', $this->id);
            })
            ->with(['tenants', 'unit'])
            ->get();

        // Extract all tenants from these leases
        $tenants = collect();
        foreach ($activeLeases as $lease) {
            $tenants = $tenants->merge($lease->tenants);
        }

        // Remove duplicates and return unique tenants
        return $tenants->unique('id');
    }

    /**
     * Get units with their current tenant information
     */
    public function unitsWithTenants()
    {
        return $this->units()->with(['leases' => function ($query) {
            $query->where('status', 'active')->with('tenants');
        }])->get();
    }

    /**
     * Get occupancy summary
     */
    public function getOccupancySummaryAttribute(): array
    {
        $totalUnits = $this->total_units;
        $occupiedUnits = $this->units()->where('status', 'occupied')->count();
        $vacantUnits = $this->units()->where('status', 'vacant')->count();
        $forLeaseUnits = $this->units()->where('status', 'for_lease')->count();
        $maintenanceUnits = $this->units()->where('status', 'maintenance')->count();
        
        return [
            'total' => $totalUnits,
            'occupied' => $occupiedUnits,
            'vacant' => $vacantUnits,
            'for_lease' => $forLeaseUnits,
            'maintenance' => $maintenanceUnits,
            'occupancy_rate' => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0
        ];
    }
}
