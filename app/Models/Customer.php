<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Model;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name', 'phone', 'address', 'photo', 'is_active', 'password',];

    // protected $casts = ['is_active' => 'boolean'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function creditSales(): HasMany
    {
        return $this->hasMany(CreditSale::class);
    }

    public function creditPayments(): HasMany
    {
        return $this->hasMany(CreditPayment::class);
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->creditSales()->sum('total_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->creditPayments()->sum('amount');
    }

    // Positive = customer owes the shop; negative = customer has advance credit.
    public function getBalanceAttribute(): float
    {
        return round($this->total_credit - $this->total_paid, 2);
    }

    public function getAdvanceAttribute(): float
    {
        return max(0, round(-$this->balance, 2));
    }
}
