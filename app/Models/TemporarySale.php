<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporarySale extends Model
{
    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(TemporarySaleItem::class);
    }
}