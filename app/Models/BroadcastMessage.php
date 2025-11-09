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

    // Accessors
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

    // Helper methods
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
            'status' => 'sent',
            'completed_at' => now()
        ]);
    }
}
