<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditSale extends Model
{
    protected $fillable = ['customer_id', 'invoice_no', 'sale_date', 'total_amount', 'notes'];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CreditSaleItem::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CreditPaymentAllocation::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->allocations()->sum('amount');
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, round((float) $this->total_amount - $this->paid_amount, 2));
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->due_amount <= 0) return 'Paid';
        if ($this->paid_amount > 0) return 'Partially Paid';
        return 'Unpaid';
    }
}
