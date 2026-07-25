<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\CashAccount;
use App\Models\Rider;
use App\Models\RiderPayment;
use Illuminate\Support\Facades\DB;

class RiderPaymentContoller extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $riderId = $request->input('rider_id');

        $query = RiderPayment::with(['rider', 'bankAccount', 'cashAccount']);

        if ($fromDate && $toDate) {
            $query = $query->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        if ($riderId) {
            $query = $query->where('rider_id', $riderId);
        }

        $payments = $query->get();

        $riders = Rider::where('status', 'active')->get();

        return view('adminPanel.rider_payments.index', compact('payments', 'fromDate', 'toDate', 'riders'));
    }

    public function create()
    {
        $riders = Rider::where('status', 'active')->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type', 1)->get();
        $cashAccounts = CashAccount::where('status', 1)->where('type', 2)->get();
        return view('adminPanel.rider_payments.create', compact('riders', 'bankAccounts', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rider_id' => 'required|integer|exists:riders,id',
            'bank_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'cash_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        // Custom validation to ensure only one of the two is filled
        if (empty($validated['bank_account_id']) && empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Either bank or cash account must be selected.'])
                ->withInput();
        }

        if (!empty($validated['bank_account_id']) && !empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Only one of bank or cash account can be selected at a time.'])
                ->withInput();
        }

        $riderPayment = RiderPayment::create([
            'rider_id' => $validated['rider_id'],
            'bank_account_id' => $validated['bank_account_id'] ?? 0,
            'cash_account_id' => $validated['cash_account_id'] ?? 0,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        $tData = [
            'transaction_id' => $riderPayment->id,
            'bank_id' => $validated['bank_account_id'] ?? 0,
            'cash_id' => $validated['cash_account_id'] ?? 0,
            'transaction_type' => 6,
            'voucher_no' => '-',
            'transaction_date' => $validated['payment_date'],
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => 0,
            'paid_amount' => 0,
            'rider_payment_amount' => $validated['amount'],
            'particular' => $validated['description'],
        ];

        DB::table('transaction')->insert($tData);

        return redirect()->route('rider.payments.index')->with('success', 'Rider Payment created successfully!');
    }

    public function edit($id)
    {
        $riderPayment = RiderPayment::with(['rider'])->findOrFail($id);
        $riders = Rider::where('status', 'active')->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type', 1)->get();
        $cashAccounts = CashAccount::where('status', 1)->where('type', 2)->get();
        return view(
            'adminPanel.rider_payments.edit',
            compact('riderPayment', 'riders', 'bankAccounts', 'cashAccounts'),
        );
    }

    public function update(Request $request, $id)
    {
        $riderPayment = RiderPayment::findOrFail($id);

        $validated = $request->validate([
            'rider_id' => 'required|integer|exists:riders,id',
            'bank_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'cash_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        // Custom validation to ensure only one of the two is filled
        if (empty($validated['bank_account_id']) && empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Either bank or cash account must be selected.'])
                ->withInput();
        }

        if (!empty($validated['bank_account_id']) && !empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Only one of bank or cash account can be selected at a time.'])
                ->withInput();
        }

        $riderPayment->update([
            'rider_id' => $validated['rider_id'],
            'bank_account_id' => $validated['bank_account_id'] ?? 0,
            'cash_account_id' => $validated['cash_account_id'] ?? 0,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        $tData = [
            'bank_id' => $validated['bank_account_id'] ?? 0,
            'transaction_type' => 6,
            'voucher_no' => '-',
            'transaction_date' => $validated['payment_date'],
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => 0,
            'paid_amount' => 0,
            'rider_payment_amount' => $validated['amount'],
            'particular' => $validated['description'],
        ];
        DB::table('transaction')->where('transaction_id', $id)->where('transaction_type', 6)->update($tData);

        return redirect()->route('rider.payments.index')->with('success', 'Rider Payment updated successfully!');
    }

    public function toggleStatus($id)
    {
        $riderPayment = RiderPayment::findOrFail($id);

        $riderPayment->status = !$riderPayment->status;
        $riderPayment->save();

        // Redirect back with a success message
        return redirect()->route('rider.payments.index')->with('success', 'Status changed successfully!');
    }

    public function getRiderBalance(Request $request)
    {
        $rider = Rider::findOrFail($request->rider_id);

        $payToRider = $rider->orders
            ->where('pay_method', '=', 2)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('shipping_charges');

        $onlineWithoutDC = $rider->orders
            ->where('pay_method', '=', 3)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('shipping_charges');

        $paidToRider = $rider->orders
            ->where('pay_method', '=', 2)
            ->where('rider_pay_status', '=', 'paid')
            ->sum('shipping_charges');

        $payToCompany = $rider->orders
            ->where('pay_method', '=', 1)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('total_amount');

        $paidToCompany = $rider->orders
            ->where('pay_method', '=', 1)
            ->where('rider_pay_status', '=', 'paid')
            ->sum('total_amount');

        return response()->json(
            [
                'payToRider' => $payToRider,
                'payToCompany' => $payToCompany,
            ],
            200,
        );
    }
}
