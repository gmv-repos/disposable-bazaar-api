<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;
use PDF;
use Auth;
use App\Models\ReceiptVoucher;

class ReceiptVoucherController extends Controller
{
    public function admin_receipt_list()
    {
        $common_data = new Array_();
        $common_data->title = 'Receipt Voucher List';
        $receiptList = DB::table('receipt_voucher as rv')
            ->join('pos_customers as poc', 'poc.id', '=', 'rv.customer_id')
            ->leftJoin('bank_and_cash_accounts as ba', 'ba.id', '=', 'rv.bank_id') // Optional bank
            ->leftJoin('bank_and_cash_accounts as ca', 'ca.id', '=', 'rv.cash_id') // Optional cash
            ->select(
                'rv.*',
                'poc.name',
                'poc.phone',
                'ba.account_name as bank_name',
                'ba.account_number',
                'ca.account_name as cash_name',
            )
            ->get();
        return view('adminPanel.pos.receipt_list')->with(compact('common_data', 'receiptList'));
    }

    public function addReceiptVoucherForm()
    {
        $customers = DB::table('sells AS s')
            ->select(
                's.customer_id',
                DB::raw('SUM(s.total_due) AS dueAmount'),
                DB::raw(
                    'COALESCE((SELECT SUM(rv.amount) FROM receipt_voucher AS rv WHERE s.customer_id = rv.customer_id), 0) AS receiptAmount',
                ),
                DB::raw(
                    '(SUM(s.total_due) - COALESCE((SELECT SUM(rv.amount) FROM receipt_voucher AS rv WHERE s.customer_id = rv.customer_id), 0)) AS balanceAmount',
                ),
                'poc.name',
                'poc.phone',
                'poc.address',
                'poc.id',
            )
            ->join('pos_customers AS poc', 's.customer_id', '=', 'poc.id')
            ->groupBy('s.customer_id', 'poc.name', 'poc.phone', 'poc.address')
            ->get();
        $banks = DB::table('bank_and_cash_accounts')->where('type', 1)->get();
        $cashs = DB::table('bank_and_cash_accounts')->where('type', 2)->get();
        return view('adminPanel.pos.addReceiptVoucherForm')->with(compact('customers', 'banks', 'cashs'));
    }

    public function loadCustomerCurrentBalance(Request $request)
    {
        $customerId = $request->input('customerId');
        $data = calculateCustomerCurrentBalance($customerId);
        return $data;
    }

    public function receiptStore(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $bank_id = $request->input('bank_id');
        $cash_id = $request->input('cash_id');
        $receipt_date = $request->input('receipt_date');
        $amount = $request->input('amount');
        $description = $request->input('description');

        $voucherNo = ReceiptVoucher::VoucherNo();

        $data['voucher_no'] = $voucherNo;
        $data['customer_id'] = $customer_id;
        $data['bank_id'] = $bank_id ?? 0;
        $data['cash_id'] = $cash_id ?? 0;
        $data['receipt_date'] = $receipt_date;
        $data['amount'] = $amount;
        $data['description'] = $description;
        $data['status'] = 1;
        $data['created_date'] = date('Y-m-d');
        $data['created_by'] = Auth::guard('admin')->user()->name;

        $receiptVoucherId = DB::table('receipt_voucher')->insertGetId($data);

        $tData = [
            'transaction_id' => $receiptVoucherId,
            'customer_id' => $customer_id,
            'bank_id' => $bank_id ?? 0,
            'cash_id' => $cash_id ?? 0,
            'transaction_type' => 2,
            'voucher_no' => $voucherNo,
            'transaction_date' => $receipt_date,
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => $amount,
            'paid_amount' => 0,
            'particular' => $description,
        ];
        DB::table('transaction')->insert($tData);

        return redirect()->back()->with('success', 'Receipt Successfully Created');
    }

    public function viewReceiptVoucherDetail(Request $request)
    {
        $id = $request->input('id');
        $receiptDetail = DB::table('receipt_voucher as rv')
            ->join('pos_customers as poc', 'poc.id', '=', 'rv.customer_id')
            ->leftJoin('bank_and_cash_accounts as ba', 'ba.id', '=', 'rv.bank_id') // Optional bank
            ->leftJoin('bank_and_cash_accounts as ca', 'ca.id', '=', 'rv.cash_id') // Optional cash
            ->select(
                'rv.*',
                'poc.name',
                'poc.email',
                'poc.phone',
                'poc.address',
                'ba.account_name as bank_name',
                'ba.account_number',
                'ca.account_name as cash_name',
            )
            ->where('rv.id', $id)
            ->first();
        return view('adminPanel.pos.viewReceiptVoucherDetail', compact('receiptDetail'));
    }
}
