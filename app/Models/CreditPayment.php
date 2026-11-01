<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditPayment extends Model
{
    protected $fillable = [
        'customer_id', 'receipt_no', 'payment_date', 'amount',
        'payment_method', 'reference', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CreditPaymentAllocation::class);
    }

    public function getAllocatedAmountAttribute(): float
    {
        return (float) $this->allocations()->sum('amount');
    }

    public function getAdvanceAmountAttribute(): float
    {
        return max(0, round((float) $this->amount - $this->allocated_amount, 2));
    }
}
