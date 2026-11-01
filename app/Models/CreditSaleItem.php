<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditSaleItem extends Model
{
    protected $fillable = [
        'credit_sale_id', 'product_id', 'product_name', 'unit',
        'quantity', 'unit_price', 'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function creditSale(): BelongsTo
    {
        return $this->belongsTo(CreditSale::class);
    }
}
