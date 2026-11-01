<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPaymentAllocation extends Model
{
    protected $fillable = ['credit_payment_id', 'credit_sale_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(CreditPayment::class, 'credit_payment_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(CreditSale::class, 'credit_sale_id');
    }
}
