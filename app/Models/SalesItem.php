<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesItem extends Model
{
    protected $fillable = [
        'sales_transaction_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'serial_number',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the sales transaction that owns the item.
     */
    public function salesTransaction(): BelongsTo
    {
        return $this->belongsTo(SalesTransaction::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price based on quantity and unit price.
     */
    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    /**
     * Get the profit for this item.
     */
    public function getProfitAttribute(): float
    {
        return ($this->unit_price - $this->product->cost_price) * $this->quantity;
    }

    /**
     * Get the profit margin for this item.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->product->cost_price == 0) {
            return 0;
        }

        return (($this->unit_price - $this->product->cost_price) / $this->product->cost_price) * 100;
    }
}
