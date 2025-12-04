<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MaintenanceRequestUpdate extends Model
{
    use HasUuids;

    protected $fillable = [
        'maintenance_request_id',
        'user_id',
        'update_type',
        'message',
        'is_internal',
        'photos',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'photos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the maintenance request this update belongs to
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    /**
     * Get the user who created this update
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this update can be edited by the given user
     */
    public function canBeEditedBy(User $user): bool
    {
        // Only the creator can edit, , Use loose comparison != instead of strict !==:
        if ($this->user_id != $user->id) {
            return false;
        }

        // Can only edit within 15 minutes
        return $this->created_at->diffInMinutes(now()) <= 15;

    }

    /**
     * Check if this update can be deleted by the given user
     */
    public function canBeDeletedBy(User $user): bool
    {
        //return true;

        // Only the creator can delete, Use loose comparison != instead of strict !==:
        if ($this->user_id != $user->id) {
            return false;
        }
        // Can only delete within 30 minutes
        return $this->created_at->diffInMinutes(now()) <= 30;

    }

    /**
     * Scope to get only public updates (visible to tenants)
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope to get only internal updates (manager-only)
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }
}
