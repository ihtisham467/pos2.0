<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'business_name',
        'address',
        'phone',
        'email',
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
     * Generate receipt number based on format.
     */
    public function generateReceiptNumber(): string
    {
        $format = $this->receipt_number_format ?? 'POS-{YYYY}-{MM}-{DD}-{0000}';
        
        $replacements = [
            '{YYYY}' => now()->format('Y'),
            '{MM}' => now()->format('m'),
            '{DD}' => now()->format('d'),
            '{0000}' => str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $format);
    }

    /**
     * Get formatted currency amount.
     */
    public function formatCurrency(float $amount): string
    {
        return $this->currency_symbol . number_format($amount, 2);
    }
}
