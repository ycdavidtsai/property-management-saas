<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BroadcastMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'sender_id',
        'title',
        'message',
        'channels',
        'recipient_type',
        'recipient_filters',
        'recipient_count',
        'status',
        'scheduled_at',
        'sent_at',
        'completed_at',
        'emails_sent',
        'emails_delivered',
        'emails_failed',
        'sms_sent',
        'sms_delivered',
        'sms_failed',
        'sms_segments_per_message',
        'sms_segments_total',
    ];

    protected $casts = [
        'channels' => 'array',
        'recipient_filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
            ->where('notifiable_type', self::class);
    }

    // =====================
    // Delivery Rate Accessors
    // =====================

    public function getDeliveryRateAttribute(): float
    {
        $total = $this->emails_sent + $this->sms_sent;
        if ($total === 0) return 0;

        $delivered = $this->emails_delivered + $this->sms_delivered;
        return round(($delivered / $total) * 100, 2);
    }

    public function getTotalSentAttribute(): int
    {
        return $this->emails_sent + $this->sms_sent;
    }

    public function getTotalDeliveredAttribute(): int
    {
        return $this->emails_delivered + $this->sms_delivered;
    }

    public function getTotalFailedAttribute(): int
    {
        return $this->emails_failed + $this->sms_failed;
    }

    // =====================
    // Channel Type Accessors
    // =====================

    /**
     * Check if this broadcast was sent via SMS
     */
    public function getIsSmsAttribute(): bool
    {
        return is_array($this->channels) && in_array('sms', $this->channels);
    }

    /**
     * Check if this broadcast was sent via Email
     */
    public function getIsEmailAttribute(): bool
    {
        return is_array($this->channels) && in_array('email', $this->channels);
    }

    /**
     * Get the primary channel type for display
     */
    public function getChannelTypeAttribute(): string
    {
        if ($this->is_sms && $this->is_email) {
            return 'both';
        }
        return $this->is_sms ? 'sms' : 'email';
    }

    /**
     * Get channel label for display
     */
    public function getChannelLabelAttribute(): string
    {
        if ($this->is_sms && $this->is_email) {
            return 'Email & SMS';
        }
        return $this->is_sms ? 'SMS' : 'Email';
    }

    // =====================
    // SMS Segment Accessors
    // =====================

    /**
     * Check if this broadcast has SMS segment data
     */
    public function getHasSegmentDataAttribute(): bool
    {
        return $this->sms_segments_total !== null && $this->sms_segments_total > 0;
    }

    /**
     * Get formatted segment info for display
     * e.g., "96 segments (2 per msg)"
     */
    public function getSegmentDisplayAttribute(): ?string
    {
        if (!$this->has_segment_data) {
            return null;
        }

        if ($this->sms_segments_per_message === 1) {
            return "{$this->sms_segments_total} segment" . ($this->sms_segments_total > 1 ? 's' : '');
        }

        return "{$this->sms_segments_total} segments ({$this->sms_segments_per_message} per msg)";
    }

    /**
     * Get estimated cost (placeholder - adjust rate as needed)
     * Default Twilio rate is approximately $0.0079 per segment
     */
    public function getEstimatedCostAttribute(): ?float
    {
        if (!$this->has_segment_data) {
            return null;
        }

        $ratePerSegment = 0.0079; // Twilio approximate rate
        return round($this->sms_segments_total * $ratePerSegment, 4);
    }

    /**
     * Get formatted estimated cost for display
     */
    public function getEstimatedCostDisplayAttribute(): ?string
    {
        $cost = $this->estimated_cost;
        if ($cost === null) {
            return null;
        }

        return '$' . number_format($cost, 2);
    }

    // =====================
    // Status Helper Methods
    // =====================

    public function markAsSending(): void
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    // =====================
    // Scopes
    // =====================

    /**
     * Scope to get only SMS broadcasts
     */
    public function scopeSmsOnly($query)
    {
        return $query->whereJsonContains('channels', 'sms');
    }

    /**
     * Scope to get only Email broadcasts
     */
    public function scopeEmailOnly($query)
    {
        return $query->whereJsonContains('channels', 'email')
                     ->whereJsonDoesntContain('channels', 'sms');
    }

    /**
     * Scope to get broadcasts with segment usage
     */
    public function scopeWithSegmentUsage($query)
    {
        return $query->whereNotNull('sms_segments_total')
                     ->where('sms_segments_total', '>', 0);
    }

    /**
     * Scope for current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('sent_at', now()->month)
                     ->whereYear('sent_at', now()->year);
    }
}
