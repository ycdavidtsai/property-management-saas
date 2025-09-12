<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantProfile extends Model
{
    // Remove HasUuids trait

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'ssn_last_four',
        'employment_status',
        'monthly_income',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'move_in_date',
        'background_check_status',
        'background_check_date',
        'credit_score',
        'notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'background_check_date' => 'date',
        'move_in_date' => 'date',
        'monthly_income' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function hasCompletedBackgroundCheck(): bool
    {
        return $this->background_check_status === 'approved';
    }
}
