<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPromotionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'request_type',  // NEW: 'promotion' or 'registration'
        'requested_by_user_id',
        'request_message',
        'requested_at',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_notes',
        'fee_amount',
        'payment_status',
        'payment_completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'fee_amount' => 'decimal:2',
    ];

    /**
     * Vendor being promoted
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * User who requested promotion (vendor user)
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Admin who reviewed the request
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if this is a registration request (self-registered vendor)
     */
    public function isRegistration(): bool
    {
        return $this->request_type === 'registration';
    }

    /**
     * Check if this is a promotion request (existing vendor)
     */
    public function isPromotion(): bool
    {
        return $this->request_type === 'promotion' || is_null($this->request_type);
    }

    /**
     * Get human-readable request type label
     */
    public function getRequestTypeLabelAttribute(): string
    {
        return match($this->request_type) {
            'registration' => 'New Registration',
            'promotion' => 'Global Promotion',
            default => 'Promotion Request',
        };
    }

    /**
     * Scope: Pending requests only
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Registration requests only (self-registered vendors)
     */
    public function scopeRegistrations($query)
    {
        return $query->where('request_type', 'registration');
    }

    /**
     * Scope: Promotion requests only (existing vendors)
     */
    public function scopePromotions($query)
    {
        return $query->where(function($q) {
            $q->where('request_type', 'promotion')
              ->orWhereNull('request_type');
        });
    }
}
