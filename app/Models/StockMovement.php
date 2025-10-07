<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'user_id',
        'reason',
        'serial_number',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'reference_id' => 'integer',
    ];

    /**
     * Get the product that owns the movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that created the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include sales movements.
     */
    public function scopeSales($query)
    {
        return $query->where('movement_type', 'sale');
    }

    /**
     * Scope a query to only include purchase movements.
     */
    public function scopePurchases($query)
    {
        return $query->where('movement_type', 'purchase');
    }

    /**
     * Scope a query to only include adjustment movements.
     */
    public function scopeAdjustments($query)
    {
        return $query->where('movement_type', 'adjustment');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the movement description.
     */
    public function getDescriptionAttribute(): string
    {
        $descriptions = [
            'sale' => 'Sale',
            'purchase' => 'Purchase',
            'adjustment' => 'Stock Adjustment',
            'return' => 'Return',
            'damage' => 'Damage',
            'loss' => 'Loss',
        ];

        return $descriptions[$this->movement_type] ?? 'Unknown';
    }

    /**
     * Get the movement direction (positive or negative).
     */
    public function getDirectionAttribute(): string
    {
        return $this->quantity_change > 0 ? 'in' : 'out';
    }
}
