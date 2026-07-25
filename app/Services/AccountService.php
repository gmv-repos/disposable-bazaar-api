<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class AccountService
{
    public static function adjustAccountBalance(int $accountId, float $amount, string $operation = '+'): object
    {
        $account = DB::table('bank_and_cash_accounts')->where('id', $accountId)->lockForUpdate()->first();

        if (!$account) {
            throw new Exception('Account not found.');
        }

        if (!in_array($operation, ['+', '-'])) {
            throw new Exception('Invalid operation. Must be "+" or "-".');
        }

        $newBalance =
            $operation === '+' ? $account->available_balance + $amount : $account->available_balance - $amount;

        if ($newBalance < 0) {
            throw new Exception('Insufficient balance.');
        }

        DB::table('bank_and_cash_accounts')
            ->where('id', $account->id)
            ->update(['available_balance' => $newBalance]);

        $account->available_balance = $newBalance;

        return $account;
    }
}
