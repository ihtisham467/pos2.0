<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'vendor_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'remaining_balance',
        'payment_status',
        'status',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'invoice_number',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_date' => 'date',
    ];

    /**
     * Get the vendor that owns the purchase.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user that created the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase items for the purchase.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Calculate and update totals.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->purchaseItems()->sum('total_cost');
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->remaining_balance = $this->total_amount - $this->amount_paid;
        
        // Update payment status
        if ($this->remaining_balance <= 0) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'pending';
        }
        
        $this->save();
    }

    /**
     * Mark purchase as received and update stock.
     */
    public function markAsReceived(): void
    {
        $this->status = 'received';
        $this->received_date = now();
        $this->save();

        // Update stock levels
        foreach ($this->purchaseItems as $item) {
            $item->product->updateStock(
                $item->quantity_received,
                'purchase',
                "Purchase received: {$this->purchase_number}",
                'purchase',
                $this->id,
                $this->user_id
            );

            // Update cost price
            $item->product->cost_price = $item->unit_cost;
            $item->product->save();
        }
    }
}
