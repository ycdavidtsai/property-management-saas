<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPromotionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
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
     * Scope: Pending requests only
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
