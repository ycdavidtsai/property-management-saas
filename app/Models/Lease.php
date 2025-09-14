<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Lease extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'organization_id',
        'unit_id',
        'start_date',
        'end_date',
        'rent_amount',
        'security_deposit',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lease_tenant', 'lease_id', 'tenant_id')
            ->where('role', 'tenant');
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'expiring_soon')
            ->orWhere(function($q) {
                $q->where('status', 'active')
                  ->where('end_date', '<=', Carbon::now()->addDays(60));
            });
    }

    // Accessors & Mutators
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->end_date <= Carbon::now()->addDays(60) && $this->status === 'active';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < Carbon::now() && in_array($this->status, ['active', 'expiring_soon']);
    }

    public function getTenantNamesAttribute(): string
    {
        return $this->tenants->pluck('name')->join(', ');
    }

    // Methods
    public function updateStatus(): void
    {
        if ($this->status === 'terminated') {
            return;
        }

        if ($this->end_date < Carbon::now()) {
            $this->update(['status' => 'expired']);
        } elseif ($this->end_date <= Carbon::now()->addDays(60)) {
            $this->update(['status' => 'expiring_soon']);
        } else {
            $this->update(['status' => 'active']);
        }
    }

    public function terminate(): void
    {
        $this->update(['status' => 'terminated']);
        $this->unit->update(['status' => 'vacant']);
    }
}
