<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequestUpdate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'maintenance_request_id',
        'user_id',
        'message',
        'photos',
        'type',
        'metadata',
        'is_internal',
    ];

    protected $casts = [
        'photos' => 'array',
        'metadata' => 'array',
        'is_internal' => 'boolean',
    ];

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
