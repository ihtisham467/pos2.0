<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'address',
        'outstanding_balance',
        'credit_limit',
        'credit_status',
        'last_payment_date',
        'total_purchases',
        'total_transactions',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'outstanding_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'last_payment_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sales transactions for the customer.
     */
    public function salesTransactions(): HasMany
    {
        return $this->hasMany(SalesTransaction::class);
    }

    /**
     * Get the payments for the customer.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include customers with outstanding balance.
     */
    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('outstanding_balance', '>', 0);
    }

    /**
     * Scope a query to search customers by name, phone, or email.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('customer_code', 'like', "%{$search}%");
        });
    }

    /**
     * Check if customer can make a purchase with given amount.
     */
    public function canPurchase(float $amount): bool
    {
        if ($this->credit_status !== 'active') {
            return false;
        }

        return ($this->outstanding_balance + $amount) <= $this->credit_limit;
    }

    /**
     * Add to outstanding balance.
     */
    public function addToOutstandingBalance(float $amount): void
    {
        $this->outstanding_balance += $amount;
        $this->total_purchases += $amount;
        $this->total_transactions += 1;
        $this->save();
    }

    /**
     * Reduce outstanding balance.
     */
    public function reduceOutstandingBalance(float $amount): void
    {
        $this->outstanding_balance = max(0, $this->outstanding_balance - $amount);
        $this->last_payment_date = now();
        $this->save();
    }

    /**
     * Get the credit utilization percentage.
     */
    public function getCreditUtilizationAttribute(): float
    {
        if ($this->credit_limit == 0) {
            return 0;
        }

        return ($this->outstanding_balance / $this->credit_limit) * 100;
    }

    /**
     * Check if customer has exceeded credit limit.
     */
    public function hasExceededCreditLimit(): bool
    {
        return $this->outstanding_balance > $this->credit_limit;
    }

    /**
     * Generate unique customer code.
     */
    public static function generateCustomerCode(): string
    {
        $prefix = 'CUST';
        $lastCustomer = static::orderBy('id', 'desc')->first();
        $nextNumber = $lastCustomer ? $lastCustomer->id + 1 : 1;
        
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
