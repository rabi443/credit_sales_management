<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryPurchaseImage extends Model
{
    protected $fillable = [
        'temporary_purchase_id',
        'image_path',
    ];

    public function temporaryPurchase(): BelongsTo
    {
        return $this->belongsTo(TemporaryPurchase::class);
    }
}
