<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'customer_id',
        'user_id',
        'subtotal',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'remaining_balance',
        'payment_status',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the transaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user that processed the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sales items for the transaction.
     */
    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }

    /**
     * Get the payments for the transaction.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include paid transactions.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include transactions with outstanding balance.
     */
    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('remaining_balance', '>', 0);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Calculate and update totals.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->salesItems()->sum('total_price');
        $this->total_amount = $this->subtotal - $this->discount_amount;
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
     * Complete the transaction.
     */
    public function complete(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();

        // Update customer balance if customer is attached
        if ($this->customer_id && $this->remaining_balance > 0) {
            $this->customer->addToOutstandingBalance($this->remaining_balance);
        }

        // Update stock levels
        foreach ($this->salesItems as $item) {
            $item->product->updateStock(
                -$item->quantity,
                'sale',
                "Sale transaction: {$this->transaction_number}",
                'sales_transaction',
                $this->id,
                $this->user_id
            );
        }
    }

    /**
     * Cancel the transaction.
     */
    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();

        // Restore stock levels
        foreach ($this->salesItems as $item) {
            $item->product->updateStock(
                $item->quantity,
                'return',
                "Cancelled transaction: {$this->transaction_number}",
                'sales_transaction',
                $this->id,
                $this->user_id
            );
        }
    }

    /**
     * Add payment to transaction.
     */
    public function addPayment(float $amount, string $paymentType = 'cash', string $notes = null): Payment
    {
        $payment = $this->payments()->create([
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'payment_reference' => $this->transaction_number,
            'notes' => $notes,
            'payment_date' => now(),
        ]);

        $this->amount_paid += $amount;
        $this->calculateTotals();
        
        // Update customer balance if it's a credit payment
        if ($this->customer_id && $paymentType === 'credit_payment') {
            $this->customer->reduceOutstandingBalance($amount);
        }

        return $payment;
    }

    /**
     * Generate unique transaction number.
     */
    public static function generateTransactionNumber(): string
    {
        $store = Store::active();
        if ($store) {
            return $store->generateReceiptNumber();
        }

        // Fallback if no store is active
        return 'POS-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
