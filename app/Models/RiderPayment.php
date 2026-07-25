<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id',
        'payment_date',
        'bank_account_id',
        'cash_account_id',
        'amount',
        'description',
        'status',
    ];

    protected $dates = ['payment_date'];

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }
}
