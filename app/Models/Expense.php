<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'expense_account_id',
        'bank_account_id',
        'cash_account_id',
        'expense_date',
        'amount',
        'description',
    ];

    protected $dates = ['expense_date'];

    public function expenseAccount()
    {
        return $this->belongsTo(ExpenseAccount::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
