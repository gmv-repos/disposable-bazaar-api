<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
    public function cashList()
    {
        $cashList = CashAccount::where('deleted', 0)->where('type', 2)->get();
        return view('adminPanel.cash_account.cash_list')->with(compact('cashList'));
    }
    public function cashStore(Request $request)
    {
        $cashAccount = new CashAccount();
        $cashAccount->account_name = $request->cash_name;
        $cashAccount->type = 2;
        $cashAccount->note = $request->note;
        $cashAccount->available_balance = $request->available_balance;
        $cashAccount->save();
        return redirect()->back()->with('success', 'Cash Account Created Successfully ');
    }

    public function cashUpdate(Request $request)
    {
        $cashAccount = CashAccount::find($request->cash_id);
        $cashAccount->account_name = $request->cash_name;
        $cashAccount->type = 2;
        $cashAccount->note = $request->note;
        $cashAccount->available_balance = $request->available_balance;
        $cashAccount->save();
        return redirect()->back()->with('success', 'Cash Account Updated d Successfully ');
    }

    public function cashActive($id)
    {
        // Find the ExpenseAccount instance by ID
        $cashAccount = CashAccount::findOrFail($id);

        // Directly set the status field manually
        $cashAccount->status = 1; // Active
        $cashAccount->save();

        // Redirect back with a success message
        return redirect()->route('admin.cash.list')->with('success', 'Cash Account activated successfully!');
    }

    public function cashInactive($id)
    {
        // Find the ExpenseAccount instance by ID
        $cashAccount = CashAccount::findOrFail($id);

        // Directly set the status field manually
        $cashAccount->status = 0; // Inactive
        $cashAccount->save();

        // Redirect back with a success message
        return redirect()->route('admin.cash.list')->with('success', 'Cash Account deactivated successfully!');
    }
}
