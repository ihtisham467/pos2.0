<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'category_id',
        'selling_price',
        'cost_price',
        'current_stock',
        'minimum_stock_level',
        'serial_number',
        'image_path',
        'track_serial_numbers',
        'is_active',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'track_serial_numbers' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sales items for the product.
     */
    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }

    /**
     * Get the purchase items for the product.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include products with low stock.
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock_level');
    }

    /**
     * Scope a query to search products by name, SKU, or barcode.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    /**
     * Get the profit margin for this product.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }
        
        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get the total stock value for this product.
     */
    public function getTotalStockValueAttribute(): float
    {
        return $this->current_stock * $this->selling_price;
    }

    /**
     * Check if the product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock_level;
    }

    /**
     * Check if the product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    /**
     * Update stock level and create stock movement record.
     */
    public function updateStock(int $quantity, string $movementType, string $reason = null, $referenceType = null, $referenceId = null, $userId = null): void
    {
        $oldStock = $this->current_stock;
        $this->current_stock += $quantity;
        $this->save();

        // Create stock movement record
        StockMovement::create([
            'product_id' => $this->id,
            'movement_type' => $movementType,
            'quantity_change' => $quantity,
            'quantity_before' => $oldStock,
            'quantity_after' => $this->current_stock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => $userId,
            'reason' => $reason,
        ]);
    }
}
