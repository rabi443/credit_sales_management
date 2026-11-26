<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryPurchaseItem extends Model
{
    protected $fillable = [
        'temporary_purchase_id',
        'product_id',
        'product_name',
        'quantity',
        'unit',
        'pieces_per_pack',
        'stock_quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'pieces_per_pack' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function temporaryPurchase(): BelongsTo
    {
        return $this->belongsTo(TemporaryPurchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
