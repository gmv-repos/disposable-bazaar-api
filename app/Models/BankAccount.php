<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    public $table = 'bank_and_cash_accounts';

    protected $fillable = [
        'account_name',
        'type',
        'account_type',
        'account_number',
        'phone',
        'branch_name',
        'note',
        'available_balance',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at',
        'deleted_by',
    ];
}
