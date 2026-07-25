<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;
use PDF;
use App\Models\PaymentVoucher;
use Illuminate\Support\Facades\Auth;

class PaymentVoucherController extends Controller
{
    public function admin_payment_list()
    {
        $common_data = new Array_();
        $common_data->title = 'Payment Voucher List';

        $paymentList = DB::table('payment_voucher as pv')
            ->join('suppliers as s', 's.id', '=', 'pv.supplier_id')
            ->leftJoin('bank_and_cash_accounts as ba', 'ba.id', '=', 'pv.bank_id') // Optional bank
            ->leftJoin('bank_and_cash_accounts as ca', 'ca.id', '=', 'pv.cash_id') // Optional cash
            ->select(
                'pv.*',
                's.supplier_name',
                's.supplier_phone_one',
                'ba.account_name as bank_name',
                'ba.account_number',
                'ca.account_name as cash_name',
            )
            ->get();

        return view('adminPanel.product_stock.payment_list')->with(compact('common_data', 'paymentList'));
    }

    public function addPaymentVoucherForm()
    {
        $suppliers = DB::table('suppliers')->get();
        $banks = DB::table('bank_and_cash_accounts')->where('type', 1)->get();
        $cashs = DB::table('bank_and_cash_accounts')->where('type', 2)->get();
        return view('adminPanel.product_stock.addPaymentVoucherForm')->with(compact('suppliers', 'banks', 'cashs'));
    }

    public function loadSupplierCurrentBalance(Request $request)
    {
        $supplierId = $request->input('supplierId');
        $data = calculateSupplierCurrentBalance($supplierId);
        return $data;
    }

    public function paymentStore(Request $request)
    {
        $bank_id = $request->input('bank_id');
        $cash_id = $request->input('cash_id');
        $supplier_id = $request->input('supplier_id');
        $payment_date = $request->input('payment_date');
        $amount = $request->input('amount');
        $description = $request->input('description');

        $voucherNo = PaymentVoucher::VoucherNo();

        $data['voucher_no'] = $voucherNo;
        $data['bank_id'] = $bank_id ?? 0;
        $data['cash_id'] = $cash_id ?? 0;
        $data['supplier_id'] = $supplier_id;
        $data['payment_date'] = $payment_date;
        $data['amount'] = $amount;
        $data['description'] = $description;
        $data['status'] = 1;
        $data['created_date'] = date('Y-m-d');
        $data['created_by'] = Auth::guard('admin')->user()->name;

        $paymentVoucherId = DB::table('payment_voucher')->insertGetId($data);

        $tData = [
            'transaction_id' => $paymentVoucherId,
            'supplier_id' => $supplier_id,
            'bank_id' => $bank_id ?? 0,
            'cash_id' => $cash_id ?? 0,
            'transaction_type' => 4,
            'voucher_no' => $voucherNo,
            'transaction_date' => $payment_date,
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => $amount,
            'paid_amount' => 0,
            'particular' => $description,
        ];
        DB::table('transaction')->insert($tData);

        return redirect()->back()->with('success', 'Payment Successfully Created');
    }

    public function viewPaymentVoucherDetail(Request $request)
    {
        $id = $request->input('id');
        $paymentDetail = DB::table('payment_voucher as pv')
            ->join('suppliers as s', 's.id', '=', 'pv.supplier_id')
            ->leftJoin('bank_and_cash_accounts as ba', 'ba.id', '=', 'pv.bank_id')
            ->leftJoin('bank_and_cash_accounts as ca', 'ca.id', '=', 'pv.cash_id')
            ->select(
                'pv.*',
                's.supplier_name',
                's.supplier_email',
                's.supplier_phone_one',
                's.supplier_address',
                'ba.account_name as bank_name',
                'ba.account_number',
                'ca.account_name as cash_name',
            )
            ->where('pv.id', $id)
            ->first();
        return view('adminPanel.product_stock.viewPaymentVoucherDetail', compact('paymentDetail'));
    }
}
