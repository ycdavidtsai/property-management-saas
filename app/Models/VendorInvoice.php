<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VendorInvoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vendor_id',
        'maintenance_request_id',
        'organization_id',
        'invoice_number',
        'amount',
        'status',
        'details',
        'notes',
        'issued_date',
        'due_date',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'overdue' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): ?string
    {
        if (!$this->payment_method) {
            return null;
        }

        return match($this->payment_method) {
            'check' => 'Check',
            'transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'zelle' => 'Zelle',
            'venmo' => 'Venmo',
            'other' => 'Other',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Check if invoice is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'paid') {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Get days until due (or days overdue if negative)
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date || $this->status === 'paid') {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->due_date, false);
    }

    // ============================================
    // STATUS CHECKS
    // ============================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || $this->is_overdue;
    }

    // ============================================
    // ACTIONS
    // ============================================

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(?string $paymentMethod = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
        ]);
    }

    /**
     * Mark invoice as overdue
     */
    public function markAsOverdue(): void
    {
        if ($this->status !== 'paid') {
            $this->update(['status' => 'overdue']);
        }
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeIssuedThisMonth($query)
    {
        return $query->whereMonth('issued_date', now()->month)
                     ->whereYear('issued_date', now()->year);
    }

    public function scopePaidThisMonth($query)
    {
        return $query->where('status', 'paid')
                     ->whereMonth('paid_at', now()->month)
                     ->whereYear('paid_at', now()->year);
    }

    // ============================================
    // STATIC HELPERS
    // ============================================

    /**
     * Generate unique invoice number
     * Format: INV-{VENDOR_SHORT}-{YYYYMM}-{SEQ}
     */
    public static function generateInvoiceNumber(Vendor $vendor): string
    {
        // Get vendor short code (first 3 chars of name, uppercase)
        $vendorCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $vendor->name), 0, 3));

        if (strlen($vendorCode) < 3) {
            $vendorCode = str_pad($vendorCode, 3, 'X');
        }

        $yearMonth = now()->format('Ym');

        // Get next sequence number for this vendor this month
        $lastInvoice = static::where('vendor_id', $vendor->id)
            ->where('invoice_number', 'like', "INV-{$vendorCode}-{$yearMonth}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract sequence number and increment
            $parts = explode('-', $lastInvoice->invoice_number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('INV-%s-%s-%03d', $vendorCode, $yearMonth, $seq);
    }

    /**
     * Create invoice from completed maintenance request
     */
    public static function createFromMaintenanceRequest(
        MaintenanceRequest $request,
        ?string $details = null,
        ?int $dueDays = 30
    ): self {
        $vendor = $request->assignedVendor;

        return static::create([
            'vendor_id' => $vendor->id,
            'maintenance_request_id' => $request->id,
            'organization_id' => $request->organization_id,
            'invoice_number' => static::generateInvoiceNumber($vendor),
            'amount' => $request->actual_cost ?? 0,
            'status' => 'pending',
            'details' => $details,
            'issued_date' => now()->toDateString(),
            'due_date' => $dueDays ? now()->addDays($dueDays)->toDateString() : null,
        ]);
    }
}
