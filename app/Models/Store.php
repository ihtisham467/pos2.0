<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'address',
        'phone',
        'email',
        'city',
        'state',
        'postal_code',
        'country',
        'business_registration_number',
        'logo_path',
        'business_hours',
        'currency',
        'currency_symbol',
        'receipt_settings',
        'receipt_footer',
        'receipt_number_format',
        'is_active',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'receipt_settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the store.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the active store instance.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate receipt number based on format.
     */
    public function generateReceiptNumber(): string
    {
        $format = $this->receipt_number_format ?? 'POS-{YYYY}-{MM}-{DD}-{0000}';

        // Generate a unique sequence number based on timestamp and microseconds
        $timestamp = now()->timestamp;
        $microseconds = substr(microtime(), 2, 6);
        $sequence = substr($timestamp.$microseconds, -6);

        $replacements = [
            '{YYYY}' => now()->format('Y'),
            '{MM}' => now()->format('m'),
            '{DD}' => now()->format('d'),
            '{0000}' => str_pad($sequence, 6, '0', STR_PAD_LEFT),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }

    /**
     * Get formatted currency amount.
     */
    public function formatCurrency(float $amount): string
    {
        return $this->currency_symbol.number_format($amount, 2);
    }
}
