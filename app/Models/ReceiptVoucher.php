<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    protected $table = 'receipt_voucher';
    use HasFactory;

    function scopeVoucherNo($query)
    {
        $prifix = 'RV';
        $maxReg = ReceiptVoucher::whereRaw('substr(`voucher_no`,-4,2) = ? and substr(`voucher_no`,-2,2) = ?', [
            date('m'),
            date('y'),
        ])->max(\DB::raw('convert(substr(`voucher_no`,4,length(substr(`voucher_no`,4))-4),signed integer)'));
        $reg = $maxReg + 1;
        return $voucherNo = $prifix . $reg . date('my');
    }
}
