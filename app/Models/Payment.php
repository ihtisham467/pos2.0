<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'sales_transaction_id',
        'customer_id',
        'user_id',
        'payment_type',
        'amount',
        'payment_reference',
        'notes',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the sales transaction that owns the payment.
     */
    public function salesTransaction(): BelongsTo
    {
        return $this->belongsTo(SalesTransaction::class);
    }

    /**
     * Get the customer that owns the payment.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user that processed the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include cash payments.
     */
    public function scopeCash($query)
    {
        return $query->where('payment_type', 'cash');
    }

    /**
     * Scope a query to only include credit payments.
     */
    public function scopeCredit($query)
    {
        return $query->where('payment_type', 'credit_payment');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
}
