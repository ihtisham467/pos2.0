<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_id',
        'outstanding_balance',
        'credit_limit',
        'payment_terms',
        'total_purchases',
        'total_orders',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'outstanding_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the purchases for the vendor.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include vendors with outstanding balance.
     */
    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('outstanding_balance', '>', 0);
    }

    /**
     * Scope a query to search vendors by name, phone, or email.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%");
        });
    }

    /**
     * Add to outstanding balance.
     */
    public function addToOutstandingBalance(float $amount): void
    {
        $this->outstanding_balance += $amount;
        $this->total_purchases += $amount;
        $this->total_orders += 1;
        $this->save();
    }

    /**
     * Reduce outstanding balance.
     */
    public function reduceOutstandingBalance(float $amount): void
    {
        $this->outstanding_balance = max(0, $this->outstanding_balance - $amount);
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
     * Check if vendor has exceeded credit limit.
     */
    public function hasExceededCreditLimit(): bool
    {
        return $this->outstanding_balance > $this->credit_limit;
    }

    /**
     * Get the average order value.
     */
    public function getAverageOrderValueAttribute(): float
    {
        if ($this->total_orders == 0) {
            return 0;
        }

        return $this->total_purchases / $this->total_orders;
    }
}
