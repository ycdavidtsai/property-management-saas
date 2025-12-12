<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = [
        'code',
        'url',
        'purpose',
        'reference_id',
        'reference_type',
        'expires_at',
        'click_count',
        'last_clicked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_clicked_at' => 'datetime',
    ];

    /**
     * Generate a unique short code
     */
    public static function generateCode(int $length = 6): string
    {
        do {
            // Use alphanumeric characters (avoiding ambiguous ones like 0, O, l, 1)
            $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create a short URL for a given destination
     */
    public static function shorten(
        string $url,
        ?string $purpose = null,
        $reference = null,
        ?\DateTime $expiresAt = null
    ): self {
        return self::create([
            'code' => self::generateCode(),
            'url' => $url,
            'purpose' => $purpose,
            'reference_id' => $reference?->id ?? $reference,
            'reference_type' => is_object($reference) ? get_class($reference) : null,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Get the full short URL
     */
    public function getShortUrlAttribute(): string
    {
        return url("/s/{$this->code}");
    }

    /**
     * Check if URL is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Record a click
     */
    public function recordClick(): void
    {
        $this->increment('click_count');
        $this->update(['last_clicked_at' => now()]);
    }

    /**
     * Find by code and validate
     */
    public static function findValidByCode(string $code): ?self
    {
        $shortUrl = self::where('code', $code)->first();

        if (!$shortUrl || $shortUrl->isExpired()) {
            return null;
        }

        return $shortUrl;
    }

    /**
     * Polymorphic relationship to referenced model
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }
}
