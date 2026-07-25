<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Party;
use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
use Throwable;
use App\Services\AccountService;
use Illuminate\Support\Facades\Http;

class VoucherController extends Controller
{
    public function index()
    {
        $parties = Party::all();
        $banks = DB::table('bank_and_cash_accounts')->where('type', 1)->get();
        $cashs = DB::table('bank_and_cash_accounts')->where('type', 2)->get();

        $data = compact('parties', 'banks', 'cashs');

        return view('adminPanel.vouchers.index', $data);
    }

    public function voucherStore(Request $request)
    {
        $validated = $request->validate([
            'party_id' => 'required',
            'voucher_date' => 'required|date',
            'voucher_type' => 'required',
            'account_type' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'description' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $bank_id = $validated['account_type'] == 'bank' ? $validated['account_id'] : 0;
            $cash_id = $validated['account_type'] == 'cash' ? $validated['account_id'] : 0;

            $tData = [];

            if ($validated['voucher_type'] == 'payment') {
                $voucherNo = PaymentVoucher::VoucherNo();

                $data['voucher_no'] = $voucherNo;
                $data['bank_id'] = $bank_id;
                $data['cash_id'] = $cash_id;
                $data['supplier_id'] = $validated['party_id'];
                $data['payment_date'] = $validated['voucher_date'];
                $data['amount'] = $validated['amount'];
                $data['description'] = $validated['description'] ?? '-';
                $data['status'] = 1;
                $data['created_date'] = date('Y-m-d');
                $data['created_by'] = 'admin';

                $paymentVoucherId = DB::table('payment_voucher')->insertGetId($data);

                $tData = [
                    'transaction_id' => $paymentVoucherId,
                    'supplier_id' => $validated['party_id'],
                    'bank_id' => $bank_id,
                    'cash_id' => $cash_id,
                    'transaction_type' => 4,
                    'voucher_no' => $voucherNo,
                    'transaction_date' => $validated['voucher_date'],
                    'gross_amount' => 0,
                    'discount_amount' => 0,
                    'payable_amount' => 0,
                    'receiveable_amount' => 0,
                    'receipt_amount' => $validated['amount'],
                    'paid_amount' => 0,
                    'particular' => $validated['description'] ?? '-',
                ];
            } else {
                $voucherNo = ReceiptVoucher::VoucherNo();

                $data['voucher_no'] = $voucherNo;
                $data['customer_id'] = $validated['party_id'];
                $data['bank_id'] = $bank_id;
                $data['cash_id'] = $cash_id;
                $data['receipt_date'] = $validated['voucher_date'];
                $data['amount'] = $validated['amount'];
                $data['description'] = $validated['description'] ?? '-';
                $data['status'] = 1;
                $data['created_date'] = date('Y-m-d');
                $data['created_by'] = 'admin';

                $receiptVoucherId = DB::table('receipt_voucher')->insertGetId($data);

                $tData = [
                    'transaction_id' => $receiptVoucherId,
                    'customer_id' => $validated['party_id'],
                    'bank_id' => $bank_id,
                    'cash_id' => $cash_id,
                    'transaction_type' => 2,
                    'voucher_no' => $voucherNo,
                    'transaction_date' => $validated['voucher_date'],
                    'gross_amount' => 0,
                    'discount_amount' => 0,
                    'payable_amount' => 0,
                    'receiveable_amount' => 0,
                    'receipt_amount' => $validated['amount'],
                    'paid_amount' => 0,
                    'particular' => $validated['description'] ?? '-',
                ];
            }

            DB::table('transaction')->insert($tData);

            $operation = $validated['voucher_type'] === 'payment' ? '-' : '+';

            AccountService::adjustAccountBalance($validated['account_id'], $validated['amount'], $operation);

            DB::commit();

            return redirect()->back()->with('success', 'Voucher created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function loadPartyBalance(Request $request)
    {
        $partyId = $request->input('partyId');

        $data = calculatePartyCurrentBalance($partyId);

        return $data;
    }

    public function inOutToAccount()
    {
        $accounts = DB::table('bank_and_cash_accounts')->get();

        $data = compact('accounts');

        return view('adminPanel.vouchers.inOutToAccount', $data);
    }

    public function inOutToAccountStore(Request $request)
    {
        $request->validate([
            'transactionType' => 'required|in:in,out',
            'accountId' => 'required|exists:bank_and_cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $accountId = $request->accountId;
        $amount = $request->amount;
        $transactionType = $request->transactionType;

        DB::beginTransaction();

        try {
            $operation = $transactionType === 'in' ? '+' : '-';

            $account = AccountService::adjustAccountBalance($accountId, $amount, $operation);

            DB::table('transaction')->insert([
                'transaction_id' => 0,
                'bank_id' => $account->type == 1 ? $account->id : 0,
                'cash_id' => $account->type == 2 ? $account->id : 0,
                'transaction_type' => $transactionType === 'in' ? 21 : 22,
                'voucher_no' => 'TRX-' . now()->format('YmdHis') . rand(100, 999),
                'transaction_date' => $request->date,
                'gross_amount' => 0,
                'discount_amount' => 0,
                'payable_amount' => 0,
                'receiveable_amount' => 0,
                'receipt_amount' => 0,
                'paid_amount' => 0,
                'amount_in' => $transactionType === 'in' ? $amount : 0,
                'amount_out' => $transactionType === 'out' ? $amount : 0,
                'particular' => $request->description ?? '-',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Transaction recorded successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function loadAccountCurrentBalance(Request $request)
    {
        $accountId = $request->input('accountId');

        $currentBalance = DB::table('bank_and_cash_accounts')->where('id', $accountId)->value('available_balance');

        return compact('currentBalance');
    }

    public function extraTransaction(Request $request)
    {
        $parties = Party::all();
        $accounts = DB::table('bank_and_cash_accounts')->get();

        $data = compact('parties', 'accounts');

        return view('adminPanel.vouchers.extraTransaction', $data);
    }

    public function extraTransactionStore(Request $request)
    {
        $validated = $request->validate([
            'voucher_type' => 'required',
            'party_id' => 'required',
            'accountType' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'voucher_date' => 'required|date',
            'description' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $operation = $validated['voucher_type'] === 23 ? '+' : '-';

            $account = AccountService::adjustAccountBalance($validated['account_id'], $validated['amount'], $operation);

            $bank_id = $account->type == 1 ? $account->id : 0;
            $cash_id = $account->type == 2 ? $account->id : 0;

            $voucherNo = 'TRX-EXT-' . now()->format('YmdHis') . rand(100, 999);

            DB::table('transaction')->insert([
                'transaction_id' => 0,
                'party_id' => $validated['party_id'],
                'bank_id' => $bank_id,
                'cash_id' => $cash_id,
                'transaction_type' => $validated['voucher_type'],
                'voucher_no' => $voucherNo,
                'transaction_date' => $validated['voucher_date'],
                'gross_amount' => 0,
                'discount_amount' => 0,
                'payable_amount' => 0,
                'receiveable_amount' => 0,
                'receipt_amount' => 0,
                'extra_trx_amount' => $validated['amount'],
                'paid_amount' => 0,
                'particular' => $validated['description'] ?? '-',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Voucher created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // public function loadPartyExtraBalance(Request $request) {}
}
