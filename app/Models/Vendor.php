<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory; // Removed HasUuids since we're using auto-incrementing IDs

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'business_type',
        'description',
        'specialties',
        'is_active',
        'hourly_rate',
        'notes',
        'user_id', // Link to users table
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'organization_id' => 'string', // UUIDs stay strings
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_vendor_id');
    }

    public function getFormattedSpecialtiesAttribute(): string
    {
        return is_array($this->specialties) ? implode(', ', $this->specialties) : '';
    }
}
