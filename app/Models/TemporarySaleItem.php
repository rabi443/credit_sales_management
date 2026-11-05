<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporarySaleItem extends Model
{
    protected $fillable = [
        'temporary_sale_id',
        'product_name',
        'quantity',
        'unit',
        'unit_price',
        'line_total',
    ];

    public function temporarySale()
    {
        return $this->belongsTo(TemporarySale::class);
    }
}