<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemporaryPurchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'supplier_name',
        'invoice_no',
        'purchase_date',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TemporaryPurchaseItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TemporaryPurchaseImage::class);
    }
}
